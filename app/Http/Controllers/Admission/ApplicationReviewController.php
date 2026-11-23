<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Institution;
use App\Services\EligibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationReviewController extends Controller
{
    public function store(Request $request, Institution $institution, Application $application, EligibilityService $eligibility): RedirectResponse
    {
        abort_unless($application->institution_id === $institution->id, 404);

        abort_unless($request->user()->institutions()->whereKey($institution->id)->wherePivot('status','active')->exists(), 403);

        $validated = $request->validate([
            'recommendation' => ['required', 'in:approve,reject,defer,manual_review'],
            'review_type' => ['required', 'in:eligibility,departmental,admissions'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $assessment = $eligibility->assess($application);

        DB::transaction(function () use ($application, $institution, $request, $validated, $assessment) {
            $application->reviews()->updateOrCreate(
                ['reviewed_by' => $request->user()->id, 'review_type' => $validated['review_type']],
                [
                    'institution_id' => $institution->id,
                    'eligibility_status' => $assessment['status'],
                    'recommendation' => $validated['recommendation'],
                    'notes' => $validated['notes'] ?? $assessment['details'],
                    'reviewed_at' => now(),
                ],
            );

            if ($application->status === 'submitted') {
                $application->transitionTo('under_review', $request->user(), 'Application review started.');
            }
        });

        return back()->with('status', 'Application review recorded.');
    }
}
