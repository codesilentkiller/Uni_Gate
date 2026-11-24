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

            do {
                $application->student_id = str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            } while (Application::withTrashed()->where('student_id', $application->student_id)->exists());

            $application->accepted_at = now();
            $application->save();
        }

        $previousStatus = $application->status;
        $becameApproved = $previousStatus !== 'approved' && $validated['status'] === 'approved';

        if ($application->status === $validated['status']) {
            if ($validated['status'] === 'documents_pending' && ! empty($validated['notes'])) {
                $application->update([
                    'correction_notes' => $validated['notes'],
                    'correction_requested_at' => now(),
                ]);
            }

            if ($validated['status'] === 'approved') {
                $this->ensureApprovalPaymentNotification($application);
            }

            return back()->with('status', 'Application status is unchanged.');
        }

        if (! $application->transitionTo($validated['status'], $request->user(), $validated['notes'] ?? null)) {
            abort(422, 'This application status transition is not permitted.');
        }

        if ($becameApproved) {
            $this->ensureApprovalPaymentNotification($application);
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

    /**
     * Mark an accepted applicant as enrolled.
     */
    public function enroll(Request $request, Institution $institution, Application $application): RedirectResponse
    {
        abort_unless($application->institution_id === $institution->id, 404);

        $validated = $request->validate([
            'course_level' => ['required', 'string', 'max:50'],
            'entry_status' => ['required', 'in:regular,transfer,postgraduate,part_time,distance'],
            'orientation_completed' => ['nullable', 'accepted'],
        ]);

        if ($application->status !== 'accepted' || ! $application->student_id) {
            abort(422, 'Only accepted applicants with a student ID can be enrolled.');
        }

        $application->update([
            'course_level' => $validated['course_level'],
            'entry_status' => $validated['entry_status'],
            'enrolled_at' => $application->enrolled_at ?? now(),
            'orientation_completed_at' => $request->boolean('orientation_completed') ? now() : null,
        ]);

        return back()->with('status', 'Applicant enrolled successfully.');
    }

    private function ensureApprovalPaymentNotification(Application $application): void
    {
        $application->loadMissing(['programme.department.faculty', 'institution', 'applicant.user']);
        $tuitionFee = $application->programme->tuition_fee ?: $application->programme->department->faculty->tuition_fee ?: 0;

        $alreadySent = $application->applicant->user->notifications()
            ->where('type', \App\Notifications\AdmissionPaymentPromptNotification::class)
            ->exists();

        if (! $alreadySent) {
            $application->applicant->user->notify(new \App\Notifications\AdmissionPaymentPromptNotification($application, (float) $tuitionFee));
        }
    }
}
