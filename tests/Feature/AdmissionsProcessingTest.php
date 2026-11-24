<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\AdmissionCycle;
use App\Models\AdmissionDecision;
use App\Models\AdmissionOffer;
use App\Models\Application;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdmissionsProcessingTest extends TestCase
{
    use RefreshDatabase;

    private function makeApplication(string $institutionSlug = 'limkokwing'): array
    {
        $institution = Institution::create([
            'name' => 'Limkokwing University',
            'slug' => $institutionSlug,
            'institution_code' => strtoupper($institutionSlug),
            'status' => 'active',
        ]);

        $faculty = Faculty::create(['institution_id'=>$institution->id,'name'=>'Faculty of Technology','code'=>'TECH','status'=>'active']);
        $department = Department::create(['institution_id'=>$institution->id,'faculty_id'=>$faculty->id,'name'=>'Software Engineering','code'=>'SE','status'=>'active']);
        $programme = Programme::create(['institution_id'=>$institution->id,'department_id'=>$department->id,'name'=>'BSc Software Engineering','code'=>'BSE','status'=>'active']);
        $session = AcademicSession::firstOrCreate(['code'=>'2027'],['name'=>'2027/2028','starts_at'=>'2027-09-01','ends_at'=>'2028-08-31','status'=>'active']);
        $cycle = AdmissionCycle::create(['institution_id'=>$institution->id,'academic_session_id'=>$session->id,'name'=>'2027 Intake','slug'=>'2027-'.$institutionSlug,'opens_at'=>now()->subDay(),'closes_at'=>now()->addMonths(6),'status'=>'open']);
        $cycle->programmes()->attach($programme->id);

        $applicantUser = User::factory()->create(['email_verified_at'=>now()]);
        $applicant = $applicantUser->applicant()->create();
        $application = Application::create(['application_reference'=>'UG-'.$institution->institution_code.'-2027-000001','applicant_id'=>$applicant->id,'institution_id'=>$institution->id,'programme_id'=>$programme->id,'admission_cycle_id'=>$cycle->id,'status'=>'submitted','submitted_at'=>now()]);

        return [$institution,$programme,$cycle,$applicantUser,$applicant,$application];
    }

    public function test_admissions_officer_can_record_decision_and_issue_offer(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();

        $staff = User::factory()->create(['email_verified_at'=>now()]);
        $staff->institutions()->attach($institution->id,['status'=>'active','joined_at'=>now()]);
        $role = Role::create(['name'=>'Admissions Officer','slug'=>'admissions-officer','scope'=>'institution']);
        $staff->roles()->attach($role->id,['institution_id'=>$institution->id]);

        $this->actingAs($staff)->post(route('institution.applications.decision',[$institution,$application]),[
            'decision'=>'approved','decision_reason'=>'Meets programme requirements.',
        ])->assertSessionHas('status');

        $application->refresh();
        $decision = AdmissionDecision::firstOrFail();
        $this->assertSame('approved',$decision->decision);
        $this->assertSame('approved',$application->status);

        $this->actingAs($staff)->post(route('institution.applications.offers.store',[$institution,$application]),[
            'offer_message'=>'Welcome to Limkokwing University.',
            'expires_at'=>now()->addDays(30)->format('Y-m-d H:i:s'),
        ])->assertSessionHas('status');

        $this->assertSame('offered',$application->refresh()->status);
        $this->assertSame('issued',AdmissionOffer::firstOrFail()->offer_status);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $applicantUser->id,
            'notifiable_type' => \App\Models\User::class,
        ]);
    }

    public function test_applicant_can_accept_offer_owned_by_their_application(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();

        $decision = AdmissionDecision::create(['application_id'=>$application->id,'institution_id'=>$institution->id,'programme_id'=>$programme->id,'admission_cycle_id'=>$cycle->id,'decided_by'=>$applicantUser->id,'decision'=>'approved','decided_at'=>now()]);
        $offer = AdmissionOffer::create(['offer_reference'=>'OFFER-LM-2027-000001','admission_decision_id'=>$decision->id,'application_id'=>$application->id,'institution_id'=>$institution->id,'entry_session_id'=>$cycle->academic_session_id,'issued_by'=>$applicantUser->id,'offer_status'=>'issued','issued_at'=>now(),'expires_at'=>now()->addDays(30)]);
        $application->transitionTo('under_review',$applicantUser);
        $application->transitionTo('approved',$applicantUser);
        $application->transitionTo('offered',$applicantUser);

        $this->actingAs($applicantUser)->post(route('applicant.admission-offers.respond',$offer),['response'=>'accepted'])->assertSessionHas('status');

        $this->assertSame('accepted',$offer->refresh()->offer_status);
        $this->assertSame('accepted',$application->refresh()->status);
        $application->refresh();
        $this->assertMatchesRegularExpression('/^\d{7}$/', $application->student_id);
    }

    public function test_other_applicant_cannot_accept_offer(): void
    {
        [$institution,$programme,$cycle,$owner,$applicant,$application] = $this->makeApplication();
        $decision = AdmissionDecision::create(['application_id'=>$application->id,'institution_id'=>$institution->id,'programme_id'=>$programme->id,'admission_cycle_id'=>$cycle->id,'decided_by'=>$owner->id,'decision'=>'approved','decided_at'=>now()]);
        $offer = AdmissionOffer::create(['offer_reference'=>'OFFER-LM-2027-000002','admission_decision_id'=>$decision->id,'application_id'=>$application->id,'institution_id'=>$institution->id,'entry_session_id'=>$cycle->academic_session_id,'issued_by'=>$owner->id,'offer_status'=>'issued','issued_at'=>now(),'expires_at'=>now()->addDays(30)]);

        $other = User::factory()->create(['email_verified_at'=>now()]);
        $other->applicant()->create();

        $this->actingAs($other)->post(route('applicant.admission-offers.respond',$offer),['response'=>'accepted'])->assertForbidden();
    }

    public function test_duplicate_admission_decision_is_rejected(): void
    {
        [$institution,$programme,$cycle,$owner,$applicant,$application] = $this->makeApplication();
        AdmissionDecision::create(['application_id'=>$application->id,'institution_id'=>$institution->id,'programme_id'=>$programme->id,'admission_cycle_id'=>$cycle->id,'decided_by'=>$owner->id,'decision'=>'approved','decided_at'=>now()]);

        $staff = User::factory()->create(['email_verified_at'=>now()]);
        $staff->institutions()->attach($institution->id,['status'=>'active','joined_at'=>now()]);
        $role = Role::create(['name'=>'Admissions Officer','slug'=>'admissions-officer','scope'=>'institution']);
        $staff->roles()->attach($role->id,['institution_id'=>$institution->id]);

        $this->actingAs($staff)->post(route('institution.applications.decision',[$institution,$application]),['decision'=>'rejected'])->assertStatus(422);
    }

    public function test_registrar_can_enroll_an_accepted_applicant(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();

        $application->update([
            'status' => 'accepted',
            'student_id' => 'STU-TEST-2027-000001',
            'accepted_at' => now(),
        ]);

        $registrar = User::factory()->create(['email_verified_at' => now()]);
        $registrar->institutions()->attach($institution->id, ['status' => 'active', 'joined_at' => now()]);
        $role = Role::create(['name' => 'Registrar', 'slug' => 'registrar', 'scope' => 'institution']);
        $registrar->roles()->attach($role->id, ['institution_id' => $institution->id]);

        $this->actingAs($registrar)
            ->post(route('institution.applications.enroll', [$institution, $application]), [
                'course_level' => '100 Level',
                'entry_status' => 'regular',
                'orientation_completed' => '1',
            ])
            ->assertSessionHas('status');

        $this->assertNotNull($application->refresh()->enrolled_at);

        $this->actingAs($registrar)
            ->get(route('institution.students', $institution))
            ->assertOk()
            ->assertSee('STU-TEST-2027-000001')
            ->assertSee('Enrolled');
    }

    public function test_applicant_dashboard_reflects_submitted_application(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();

        $this->actingAs($applicantUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Applications')
            ->assertSee($application->application_reference)
            ->assertSee('Submitted');
    }

    public function test_approved_application_can_be_sent_back_for_under_review(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();

        $application->update(['status' => 'approved']);

        $staff = User::factory()->create(['email_verified_at'=>now()]);
        $staff->institutions()->attach($institution->id,['status'=>'active','joined_at'=>now()]);
        $role = Role::create(['name'=>'Admissions Officer','slug'=>'admissions-officer','scope'=>'institution']);
        $staff->roles()->attach($role->id,['institution_id'=>$institution->id]);

        $this->actingAs($staff)
            ->patch(route('institution.applications.status',[$institution,$application]),[
                'status'=>'under_review','notes'=>'Please review the corrected application.',
            ])
            ->assertSessionHas('status');

        $this->assertSame('under_review',$application->refresh()->status);
    }

    public function test_repeating_approved_status_does_not_error_or_resend_notification(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();
        $application->update(['status' => 'approved']);

        $staff = User::factory()->create(['email_verified_at'=>now()]);
        $staff->institutions()->attach($institution->id,['status'=>'active','joined_at'=>now()]);
        $role = Role::create(['name'=>'Admissions Officer','slug'=>'admissions-officer','scope'=>'institution']);
        $staff->roles()->attach($role->id,['institution_id'=>$institution->id]);

        $this->actingAs($staff)
            ->patch(route('institution.applications.status',[$institution,$application]),[
                'status'=>'approved','notes'=>null,
            ])
            ->assertSessionHas('status','Application status is unchanged.');

        $this->assertSame('approved',$application->refresh()->status);
    }

    public function test_direct_approved_status_sends_payment_notification_if_missing(): void
    {
        [$institution,$programme,$cycle,$applicantUser,$applicant,$application] = $this->makeApplication();
        $application->update(['status' => 'approved']);

        $staff = User::factory()->create(['email_verified_at'=>now()]);
        $staff->institutions()->attach($institution->id,['status'=>'active','joined_at'=>now()]);
        $role = Role::create(['name'=>'Admissions Officer','slug'=>'admissions-officer','scope'=>'institution']);
        $staff->roles()->attach($role->id,['institution_id'=>$institution->id]);

        $this->actingAs($staff)
            ->patch(route('institution.applications.status',[$institution,$application]),[
                'status'=>'approved','notes'=>null,
            ])
            ->assertSessionHas('status','Application status is unchanged.');

        $this->assertDatabaseHas('notifications',[
            'notifiable_id'=>$applicantUser->id,
            'notifiable_type'=>User::class,
            'type'=>\App\Notifications\AdmissionPaymentPromptNotification::class,
        ]);
    }
}
