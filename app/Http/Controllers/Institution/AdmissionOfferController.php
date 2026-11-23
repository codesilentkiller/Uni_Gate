<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\AdmissionOffer;
use App\Models\Application;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdmissionOfferController extends Controller
{
    public function index(Institution $institution): View
    {
        $offers = $institution->admissionOffers()->with(['application.applicant.user','application.programme'])->latest()->paginate(20);
        return view('institution.admission-offers', compact('institution','offers'));
    }

    public function store(Request $request, Institution $institution, Application $application): RedirectResponse
    {
        abort_unless($application->institution_id === $institution->id, 404);

        $decision = $application->decision;
        abort_unless($decision && $decision->decision === 'approved', 422, 'Only approved applications can receive an offer.');
        abort_if($application->offer()->exists(), 422, 'An admission offer already exists.');

        $authorized = $request->user()->institutions()->whereKey($institution->id)->wherePivot('status','active')->exists()
            && $request->user()->roles()->whereIn('roles.slug',['university-administrator','admissions-officer'])
                ->where(function ($query) use ($institution) {
                    $query->whereNull('user_roles.institution_id')->orWhere('user_roles.institution_id',$institution->id);
                })->exists();
        abort_unless($authorized, 403);

        $validated = $request->validate([
            'offer_message' => ['nullable','string','max:5000'],
            'expires_at' => ['nullable','date','after:now'],
        ]);

        DB::transaction(function () use ($application,$institution,$decision,$request,$validated) {
            $number = AdmissionOffer::withTrashed()->where('institution_id',$institution->id)->count()+1;
            $offer = AdmissionOffer::create([
                'offer_reference' => 'OFFER-'.$institution->institution_code.'-'.now()->format('Y').'-'.str_pad((string)$number,6,'0',STR_PAD_LEFT),
                'admission_decision_id' => $decision->id,
                'application_id' => $application->id,
                'institution_id' => $institution->id,
                'entry_session_id' => $application->admissionCycle->academic_session_id,
                'issued_by' => $request->user()->id,
                'offer_status' => 'issued',
                'offer_message' => $validated['offer_message'] ?? null,
                'issued_at' => now(),
                'expires_at' => $validated['expires_at'] ?? now()->addDays(30),
            ]);

            $offer->history()->create([
                'institution_id' => $institution->id,
                'changed_by' => $request->user()->id,
                'from_status' => null,
                'to_status' => 'issued',
                'notes' => 'Admission offer issued.',
                'changed_at' => now(),
            ]);

            $application->transitionTo('offered',$request->user(),'Admission offer issued.');
            $application->applicant->user->notify(new \App\Notifications\AdmissionOfferIssuedNotification($offer));
        });

        return back()->with('status','Admission offer issued.');
    }

    public function withdraw(Request $request, Institution $institution, AdmissionOffer $offer): RedirectResponse
    {
        abort_unless($offer->institution_id === $institution->id, 404);

        $authorized = $request->user()->institutions()->whereKey($institution->id)->wherePivot('status','active')->exists()
            && $request->user()->roles()->whereIn('roles.slug',['university-administrator','admissions-officer'])
                ->where(function ($query) use ($institution) {
                    $query->whereNull('user_roles.institution_id')->orWhere('user_roles.institution_id',$institution->id);
                })->exists();
        abort_unless($authorized, 403);

        abort_unless(in_array($offer->offer_status,['draft','issued'],true),422,'This offer cannot be withdrawn.');

        DB::transaction(function () use ($offer,$institution,$request) {
            $from = $offer->offer_status;
            $offer->update(['offer_status'=>'withdrawn','withdrawn_at'=>now(),'withdrawal_reason'=>$request->input('reason')]);
            $offer->history()->create(['institution_id'=>$institution->id,'changed_by'=>$request->user()->id,'from_status'=>$from,'to_status'=>'withdrawn','notes'=>$request->input('reason'),'changed_at'=>now()]);
            $offer->application->transitionTo('rejected',$request->user(),'Admission offer withdrawn.');
        });

        return back()->with('status','Admission offer withdrawn.');
    }
}
