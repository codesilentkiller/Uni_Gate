<?php

namespace Tests\Feature;

use App\Models\AdmissionCycle;
use App\Models\Application;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeInstitutionWithProgramme(string $slug = 'limkokwing'): array
    {
        $institution = Institution::create([
            'name' => 'Limkokwing University',
            'slug' => $slug,
            'institution_code' => strtoupper($slug),
            'status' => 'active',
        ]);

        $faculty = Faculty::create([
            'institution_id' => $institution->id,
            'name' => 'Faculty of Technology',
            'code' => 'TECH',
            'status' => 'active',
        ]);

        $department = Department::create([
            'institution_id' => $institution->id,
            'faculty_id' => $faculty->id,
            'name' => 'Software Engineering',
            'code' => 'SE',
            'status' => 'active',
        ]);

        $programme = Programme::create([
            'institution_id' => $institution->id,
            'department_id' => $department->id,
            'name' => 'BSc Software Engineering',
            'code' => 'BSE',
            'status' => 'active',
        ]);

        $session = \App\Models\AcademicSession::firstOrCreate(
            ['code' => '2027'],
            [
                'name' => '2027/2028',
                'starts_at' => '2027-09-01',
                'ends_at' => '2028-08-31',
                'status' => 'active',
            ],
        );

        $cycle = AdmissionCycle::create([
            'institution_id' => $institution->id,
            'academic_session_id' => $session->id,
            'name' => '2027 Intake',
            'slug' => '2027',
            'opens_at' => now()->subDay(),
            'closes_at' => now()->addMonths(6),
            'status' => 'open',
        ]);

        $cycle->programmes()->attach($programme->id);

        return [$institution, $programme, $cycle];
    }

    public function test_applicant_can_create_and_submit_draft_application(): void
    {
        [$institution, $programme, $cycle] = $this->makeInstitutionWithProgramme();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $applicant = $user->applicant()->create();

        $wassce = \Illuminate\Http\UploadedFile::fake()->create('wassce.pdf', 100, 'application/pdf');
        $idCard = \Illuminate\Http\UploadedFile::fake()->create('national-id.pdf', 100, 'application/pdf');

        \Illuminate\Support\Facades\Storage::fake('unigate-private');

        $response = $this->actingAs($user)->post(route('applicant.applications.store'), [
            'institution_id' => $institution->id,
            'admission_cycle_id' => $cycle->id,
            'programme_id' => $programme->id,
            'entry_level' => '100 Level',
            'personal_statement' => 'I want to study software engineering.',
            'wassce_certificate' => $wassce,
            'id_card' => $idCard,
        ]);

        $response->assertSessionHasNoErrors();

        $application = Application::firstOrFail();
        $response->assertRedirect(route('applicant.applications.show', $application));
        $this->assertSame('draft', $application->status);

        $this->actingAs($user)
            ->post(route('applicant.applications.submit', $application))
            ->assertSessionHas('status');

        $this->assertSame('submitted', $application->refresh()->status);
        $this->assertCount(2, $application->statusHistory);
        $this->assertCount(2, $application->documents);
    }

    public function test_duplicate_application_is_rejected(): void
    {
        [$institution, $programme, $cycle] = $this->makeInstitutionWithProgramme();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $applicant = $user->applicant()->create();

        Application::create([
            'application_reference' => 'UG-LM-2027-000001',
            'applicant_id' => $applicant->id,
            'institution_id' => $institution->id,
            'programme_id' => $programme->id,
            'admission_cycle_id' => $cycle->id,
            'status' => 'draft',
        ]);

        $this->actingAs($user)->post(route('applicant.applications.store'), [
            'institution_id' => $institution->id,
            'admission_cycle_id' => $cycle->id,
            'programme_id' => $programme->id,
        ])->assertSessionHasErrors();
    }

    public function test_institution_staff_cannot_access_another_institution_application(): void
    {
        [$institutionA, $programmeA, $cycleA] = $this->makeInstitutionWithProgramme('limkokwing');
        [$institutionB, $programmeB, $cycleB] = $this->makeInstitutionWithProgramme('university-b');

        $applicantUser = User::factory()->create(['email_verified_at' => now()]);
        $applicant = $applicantUser->applicant()->create();

        $application = Application::create([
            'application_reference' => 'UG-LIM-2027-000001',
            'applicant_id' => $applicant->id,
            'institution_id' => $institutionA->id,
            'programme_id' => $programmeA->id,
            'admission_cycle_id' => $cycleA->id,
            'status' => 'submitted',
        ]);

        $staff = User::factory()->create(['email_verified_at' => now()]);
        $staff->institutions()->attach($institutionB->id, ['status' => 'active', 'joined_at' => now()]);
        $role = Role::create(['name' => 'Admissions Officer', 'slug' => 'admissions-officer', 'scope' => 'institution']);
        $staff->roles()->attach($role->id, ['institution_id' => $institutionB->id]);

        $this->actingAs($staff)
            ->get(route('institution.applications.show', [$institutionB, $application]))
            ->assertNotFound();
    }

    public function test_invalid_application_status_transition_is_rejected(): void
    {
        [$institution, $programme, $cycle] = $this->makeInstitutionWithProgramme();
        $applicantUser = User::factory()->create();
        $applicant = $applicantUser->applicant()->create();

        $application = Application::create([
            'application_reference' => 'UG-LM-2027-000002',
            'applicant_id' => $applicant->id,
            'institution_id' => $institution->id,
            'programme_id' => $programme->id,
            'admission_cycle_id' => $cycle->id,
            'status' => 'draft',
        ]);

        $this->assertFalse($application->transitionTo('offered'));
        $this->assertSame('draft', $application->refresh()->status);
    }
}
