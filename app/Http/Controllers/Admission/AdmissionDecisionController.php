<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\AdmissionDecision;
use App\Models\Application;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionDecisionController extends Controller
{
    public function store(Request $request, Institution $institution, Application $application): RedirectResponse
    {
        abort_unless($application->institution_id === $institution->id, 404);

        $authorized = $request->user()->institutions()->whereKey($institution->id)->wherePivot('status','active')->exists()
            && $request->user()->roles()->whereIn('roles.slug',['university-administrator','admissions-officer'])
                ->where(function ($query) use ($institution) {
                    $query->whereNull('user_roles.institution_id')->orWhere('user_roles.institution_id',$institution->id);
                })->exists();

        abort_unless($authorized, 403);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected,deferred,waitlisted'],
            'decision_reason' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($application, $institution, $request, $validated) {
            $application = Application::whereKey($application->id)->lockForUpdate()->firstOrFail();

            abort_unless($application->institution_id === $institution->id, 404);
            abort_if($application->decision()->exists(), 422, 'A final admission decision already exists.');

            $decision = AdmissionDecision::create([
                'application_id' => $application->id,
                'institution_id' => $institution->id,
                'programme_id' => $application->programme_id,
                'admission_cycle_id' => $application->admission_cycle_id,
                'decided_by' => $request->user()->id,
                'approved_by' => $validated['decision'] === 'approved' ? $request->user()->id : null,
                'decision' => $validated['decision'],
                'decision_reason' => $validated['decision_reason'] ?? null,
                'decided_at' => now(),
            ]);

            if ($application->status === 'submitted') {
                if (! $application->transitionTo('under_review', $request->user(), 'Application moved to admissions review.')) {
                    throw new \RuntimeException('Unable to move application to under-review status.');
                }
            }

            if ($validated['decision'] === 'approved') {
                if (! $application->transitionTo('approved', $request->user(), 'Admission approved.')) {
                    throw new \RuntimeException('Unable to transition application to approved status.');
                }
            } elseif ($validated['decision'] === 'rejected') {
                if (! $application->transitionTo('rejected', $request->user(), 'Application rejected.')) {
                    throw new \RuntimeException('Unable to transition application to rejected status.');
                }
            }

            $decision->application->applicant->user->notify(new \App\Notifications\AdmissionDecisionNotification($decision));
        });

        return back()->with('status', 'Admission decision recorded.');
    }
}
