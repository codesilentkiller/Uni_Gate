<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Institution $institution): View
    {
        $applications = $institution->applications()
            ->with(['applicant.user', 'programme.department', 'admissionCycle'])
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->integer('programme_id'), fn ($query, $programmeId) => $query->where('programme_id', $programmeId))
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('application_reference', 'like', "%{$search}%")
                        ->orWhereHas('applicant.user', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        })
                        ->orWhereHas('applicant', fn ($query) => $query->where('national_id', 'like', "%{$search}%"));
                });
            })
            ->when($request->string('eligibility')->toString(), function ($query, $eligibility) {
                $query->whereHas('reviews', fn ($query) => $query->where('eligibility_status', $eligibility));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $programmes = $institution->programmes()->where('status', 'active')->orderBy('name')->get();

        return view('institution.applications', compact('institution', 'applications', 'programmes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution, Application $application): View
    {
        abort_unless($application->institution_id === $institution->id, 404);

        $application->load([
            'applicant.user',
            'programme.department.faculty',
            'admissionCycle.academicSession',
            'statusHistory.changedBy',
            'applicant.examinationResults',
            'reviews',
        ]);

        return view('institution.applications-show', compact('institution', 'application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Update the status of the application.
     */
    public function updateStatus(Request $request, Institution $institution, Application $application): RedirectResponse
    {
        abort_unless($application->institution_id === $institution->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:under_review,documents_pending,awaiting_payment,approved,offered,accepted,rejected,withdrawn'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validated['status'] === 'accepted' && $application->status !== 'approved') {
            abort(422, 'Only approved applications can be accepted directly by the university.');
        }

        if ($validated['status'] === 'accepted' && ! $application->student_id) {
            $acceptedCount = Application::withTrashed()
                ->where('institution_id', $institution->id)
                ->whereNotNull('student_id')
                ->count();

            $application->student_id = 'STU-'.$institution->institution_code.'-'.now()->format('Y').'-'.str_pad((string) ($acceptedCount + 1), 6, '0', STR_PAD_LEFT);
            $application->accepted_at = now();
            $application->save();
        }

        if (! $application->transitionTo($validated['status'], $request->user(), $validated['notes'] ?? null)) {
            abort(422, 'This application status transition is not permitted.');
        }

        return back()->with('status', 'Application status updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
