<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\AdmissionCycle;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createApplicationFixture(string $institutionSlug = 'limkokwing'): array
    {
        $institution = Institution::create([
            'name' => 'Limkokwing University',
            'slug' => $institutionSlug,
            'institution_code' => strtoupper($institutionSlug),
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

        $session = AcademicSession::firstOrCreate([
            'code' => '2027',
        ], [
            'name' => '2027/2028',
            'starts_at' => '2027-09-01',
            'ends_at' => '2028-08-31',
            'status' => 'active',
        ]);

        $cycle = AdmissionCycle::create([
            'institution_id' => $institution->id,
            'academic_session_id' => $session->id,
            'name' => '2027 Intake',
            'slug' => '2027-'.$institutionSlug,
            'opens_at' => now()->subDay(),
            'closes_at' => now()->addMonths(6),
            'status' => 'open',
        ]);
        $cycle->programmes()->attach($programme->id);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $applicant = $user->applicant()->create();

        $application = Application::create([
            'application_reference' => 'UG-'.$institution->institution_code.'-2027-000001',
            'applicant_id' => $applicant->id,
            'institution_id' => $institution->id,
            'programme_id' => $programme->id,
            'admission_cycle_id' => $cycle->id,
            'status' => 'draft',
        ]);

        $documentType = DocumentType::create([
            'institution_id' => $institution->id,
            'name' => 'Passport Photograph',
            'slug' => 'passport-photograph',
            'allowed_extensions' => 'pdf,jpg,jpeg,png',
            'max_file_size_kb' => 5120,
            'status' => 'active',
        ]);

        return [$institution, $programme, $cycle, $user, $applicant, $application, $documentType];
    }

    public function test_applicant_can_upload_a_private_document(): void
    {
        Storage::fake('unigate-private');
        [$institution, $programme, $cycle, $user, $applicant, $application, $documentType] = $this->createApplicationFixture();

        $response = $this->actingAs($user)->post(
            route('applicant.applications.documents.store', $application),
            [
                'document_type_id' => $documentType->id,
                'document' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
            ],
        );

        $response->assertSessionHasNoErrors();
        $document = ApplicationDocument::firstOrFail();
        $this->assertSame('uploaded', $document->status);
        $this->assertSame($application->id, $document->application_id);
        Storage::disk('unigate-private')->assertExists($document->stored_path);
        $this->assertCount(1, $document->verificationHistory);
    }

    public function test_applicant_cannot_upload_for_another_applicant_application(): void
    {
        Storage::fake('unigate-private');
        [$institution, $programme, $cycle, $owner, $applicant, $application, $documentType] = $this->createApplicationFixture();
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        $otherUser->applicant()->create();

        $this->actingAs($otherUser)->post(
            route('applicant.applications.documents.store', $application),
            [
                'document_type_id' => $documentType->id,
                'document' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
            ],
        )->assertForbidden();

        $this->assertDatabaseCount('application_documents', 0);
    }

    public function test_institution_staff_cannot_download_another_institution_document(): void
    {
        Storage::fake('unigate-private');
        [$institutionA, $programmeA, $cycleA, $owner, $applicant, $applicationA, $documentTypeA] = $this->createApplicationFixture('limkokwing');

        $institutionB = Institution::create([
            'name' => 'University B',
            'slug' => 'university-b',
            'institution_code' => 'UB',
            'status' => 'active',
        ]);

        $document = ApplicationDocument::create([
            'application_id' => $applicationA->id,
            'institution_id' => $institutionA->id,
            'applicant_id' => $applicant->id,
            'document_type_id' => $documentTypeA->id,
            'uploaded_by' => $owner->id,
            'disk' => 'unigate-private',
            'stored_path' => 'institutions/1/applications/1/passport.pdf',
            'original_name' => 'passport.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 100,
            'checksum' => hash('sha256', 'test'),
            'version' => 1,
            'status' => 'uploaded',
        ]);

        Storage::disk('unigate-private')->put($document->stored_path, 'private-document');

        $staff = User::factory()->create(['email_verified_at' => now()]);
        $staff->institutions()->attach($institutionB->id, ['status' => 'active', 'joined_at' => now()]);
        $role = Role::create(['name' => 'Document Verification Officer', 'slug' => 'document-verification-officer', 'scope' => 'institution']);
        $staff->roles()->attach($role->id, ['institution_id' => $institutionB->id]);

        $this->actingAs($staff)
            ->get(route('institution.application-documents.download', [$institutionB, $document]))
            ->assertNotFound();
    }
}
