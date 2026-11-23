<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AdmissionOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdmissionOfferController extends Controller
{
    public function index(Request $request): View
    {
        $offers = AdmissionOffer::with(['institution','application.programme'])
            ->whereHas('application', fn ($query) => $query->where('applicant_id',$request->user()->applicant?->id))
            ->latest()
            ->get();

        return view('applicant.admission-offers', compact('offers'));
    }

    public function respond(Request $request, AdmissionOffer $offer): RedirectResponse
    {
        abort_unless($request->user()->applicant?->id === $offer->application->applicant_id, 403);
        abort_unless($offer->offer_status === 'issued', 422, 'This offer is not available for response.');

        $validated = $request->validate(['response' => ['required','in:accepted,declined']]);

        if ($validated['response'] === 'accepted' && $offer->expires_at && now()->gt($offer->expires_at)) {
            $offer->update(['offer_status'=>'expired','withdrawn_at'=>null]);
            abort(422, 'This admission offer has expired.');
        }

        DB::transaction(function () use ($offer,$request,$validated) {
            $from = $offer->offer_status;
            $offer->update([
                'offer_status' => $validated['response'],
                'accepted_at' => $validated['response'] === 'accepted' ? now() : null,
                'declined_at' => $validated['response'] === 'declined' ? now() : null,
            ]);

            $offer->history()->create([
                'institution_id'=>$offer->institution_id,
                'changed_by'=>$request->user()->id,
                'from_status'=>$from,
                'to_status'=>$validated['response'],
                'notes'=>'Applicant responded to admission offer.',
                'changed_at'=>now(),
            ]);

            if ($validated['response'] === 'accepted') {
                $application = $offer->application;

                if (! $application->student_id) {
                    $acceptedCount = \App\Models\Application::withTrashed()
                        ->where('institution_id', $application->institution_id)
                        ->whereNotNull('student_id')
                        ->count();

                    $application->student_id = 'STU-'.$application->institution->institution_code.'-'.$application->admissionCycle->academicSession->starts_at->format('Y').'-'.str_pad((string) ($acceptedCount + 1), 6, '0', STR_PAD_LEFT);
                    $application->accepted_at = now();
                    $application->save();
                }
            }

            $offer->application->transitionTo(
                $validated['response'] === 'accepted' ? 'accepted' : 'declined',
                $request->user(),
                $validated['response'] === 'accepted' ? 'Admission offer accepted.' : 'Admission offer declined.',
            );
        });

        return back()->with('status','Offer response recorded.');
    }
}
