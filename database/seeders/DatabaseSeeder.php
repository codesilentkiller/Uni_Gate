<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\AdmissionCycle;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\Permission;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = [
            ['Manage universities', 'manage-universities', 'platform'],
            ['Manage platform users', 'manage-platform-users', 'platform'],
            ['View audit logs', 'view-audit-logs', 'platform'],
            ['Manage institution staff', 'manage-institution-staff', 'institution'],
            ['Manage programmes', 'manage-programmes', 'institution'],
            ['Manage admission cycles', 'manage-admission-cycles', 'institution'],
            ['View applications', 'view-applications', 'admission'],
            ['Review applications', 'review-applications', 'admission'],
            ['Issue admission offers', 'issue-admission-offers', 'admission'],
            ['Manage student records', 'manage-student-records', 'registration'],
            ['Approve registrations', 'approve-registrations', 'registration'],
            ['Manage payments', 'manage-payments', 'finance'],
            ['Verify payments', 'verify-payments', 'finance'],
            ['Verify documents', 'verify-documents', 'documents'],
            ['Verify academic eligibility', 'verify-academic-eligibility', 'admission'],
            ['Recommend admission decisions', 'recommend-admission-decisions', 'admission'],
            ['Approve admission decisions', 'approve-admission-decisions', 'admission'],
            ['Reject admission decisions', 'reject-admission-decisions', 'admission'],
            ['Withdraw admission offers', 'withdraw-admission-offers', 'admission'],
            ['Respond to admission offers', 'respond-to-admission-offers', 'applicant'],
        ];

        foreach ($permissions as [$name, $slug, $group]) {
            Permission::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'group' => $group,
            ]);
        }

        $roles = [
            ['Super Administrator', 'super-administrator', 'platform'],
            ['Platform Support Administrator', 'platform-support-administrator', 'platform'],
            ['Platform Auditor', 'platform-auditor', 'platform'],
            ['University Administrator', 'university-administrator', 'institution'],
            ['Admissions Officer', 'admissions-officer', 'institution'],
            ['Registrar', 'registrar', 'institution'],
            ['Finance Officer', 'finance-officer', 'institution'],
            ['Department/Faculty Officer', 'department-faculty-officer', 'institution'],
            ['Document Verification Officer', 'document-verification-officer', 'institution'],
            ['Read-only Staff', 'read-only-staff', 'institution'],
            ['Applicant', 'applicant', 'applicant'],
            ['Student', 'student', 'applicant'],
        ];

        foreach ($roles as [$name, $slug, $scope]) {
            Role::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'scope' => $scope,
            ]);
        }

        $superAdmin = Role::where('slug', 'super-administrator')->firstOrFail();
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));

        $limkokwing = Institution::firstOrCreate(
            ['slug' => 'limkokwing-university-sierra-leone'],
            [
                'name' => 'Limkokwing University of Creative Technology, Sierra Leone',
                'institution_code' => 'LK-SL',
                'status' => 'active',
            ],
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@unigate.local'],
            [
                'first_name' => 'UniGate',
                'last_name' => 'Administrator',
                'phone' => null,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
        );

        $admin->roles()->syncWithoutDetaching([
            $superAdmin->id => ['institution_id' => null],
        ]);

        Institution::firstOrCreate(
            ['slug' => 'university-b'],
            [
                'name' => 'University B',
                'institution_code' => 'UB',
                'status' => 'active',
            ],
        );

        $universityAdminRole = Role::where('slug', 'university-administrator')->firstOrFail();
        $limkokwingAdmin = User::firstOrCreate(
            ['email' => 'limkokwing.admin@unigate.local'],
            [
                'first_name' => 'Limkokwing',
                'last_name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],
        );
        $limkokwing->users()->syncWithoutDetaching([
            $limkokwingAdmin->id => ['status' => 'active', 'joined_at' => now()],
        ]);
        $limkokwingAdmin->roles()->syncWithoutDetaching([
            $universityAdminRole->id => ['institution_id' => $limkokwing->id],
        ]);

        $session = AcademicSession::firstOrCreate(
            ['code' => '2027/2028'],
            [
                'name' => '2027/2028 Academic Session',
                'starts_at' => '2027-09-01',
                'ends_at' => '2028-08-31',
                'status' => 'active',
            ],
        );

        $obsoleteDepartment = Department::withTrashed()
            ->where('institution_id', $limkokwing->id)
            ->where('code', 'SE')
            ->first();

        if ($obsoleteDepartment) {
            $obsoleteDepartment->delete();
        }

        $obsoleteFaculty = Faculty::withTrashed()
            ->where('institution_id', $limkokwing->id)
            ->where('code', 'FIT')
            ->first();

        if ($obsoleteFaculty) {
            $obsoleteFaculty->delete();
        }

        $obsoleteLimkokwingProgramme = Programme::withTrashed()
            ->where('institution_id', $limkokwing->id)
            ->where('code', 'BSE')
            ->first();

        if ($obsoleteLimkokwingProgramme) {
            \App\Models\DocumentRequirement::where('institution_id', $limkokwing->id)
                ->where('programme_id', $obsoleteLimkokwingProgramme->id)
                ->delete();
            $obsoleteLimkokwingProgramme->admissionCycles()->detach();
            $obsoleteLimkokwingProgramme->delete();
        }

        $catalogue = [
            ['LKBMG', 'Business Management and Globalisation', 'LKBM', 'Business Management and Globalisation', [
                ['LKBBAF', 'Bachelor of Business (Hons) in Accounting & Finance', 'Bachelor (Hons)', 4],
                ['LKBBBA', 'Bachelor of Business (Hons) in Business Administration', 'Bachelor (Hons)', 4],
                ['LKBTM', 'Bachelor of Art (Hons) in Tourism Management', 'Bachelor of Arts (Hons)', 4],
                ['LKBE', 'Bachelor of Business in Entrepreneurship', 'Bachelor', 4],
                ['LKIB', 'International Business', 'Bachelor', 4],
            ]],
            ['LKICT', 'Faculty of Information and Communication Technology', 'LKICT', 'Information and Communication Technology', [
                ['LKBIT', 'Bachelor of Science (Hons) in Information Technology', 'Bachelor of Science (Hons)', 4],
                ['LKBBIT', 'Bachelor of Science in Business Information Technology', 'Bachelor of Science', 4],
                ['LKBSEMM', 'Bachelor of Science (Hons) in Software Engineering with Multimedia', 'Bachelor of Science (Hons)', 4],
                ['LKDIT', 'Diploma in Information Technology', 'Diploma', 2],
                ['LKDBIT', 'Diploma in Business Information Technology', 'Diploma', 2],
                ['LKDMSE', 'Diploma in Multimedia and Software Engineering', 'Diploma', 2],
            ]],
            ['LKCMPR', 'Faculty of Communication, Media, Broadcasting and Public Relations', 'LKCMPR', 'Communication, Media, Broadcasting and Public Relations', [
                ['LKBJ', 'BA (Hons) Broadcasting & Journalism', 'Bachelor of Arts (Hons)', 4],
                ['LKPC', 'BA (Hons) Professional Communication', 'Bachelor of Arts (Hons)', 4],
                ['LKDF', 'Bachelor of Arts in Digital Film', 'Bachelor of Arts', 4],
                ['LKDBR', 'Diploma in Broadcasting (TV & Radio)', 'Diploma', 2],
            ]],
            ['LKADI', 'Faculty of Architecture and Design Innovation', 'LKADI', 'Architecture and Design Innovation', [
                ['LKIA', 'Bachelor of Art (Hons) in Interior Architecture', 'Bachelor of Arts (Hons)', 4],
                ['LKDAT', 'Diploma in Architectural Technology', 'Diploma', 2],
            ]],
            ['LKCDM', 'Faculty of Creative Design and Multimedia', 'LKCDM', 'Creative Design and Multimedia', [
                ['LKAGD', 'Associate Degree in Graphic Design', 'Associate Degree', 2],
                ['LKAFD', 'Associate Degree in Fashion and Apparel Design', 'Associate Degree', 2],
                ['LKACAD', 'Associate Degree in Creative Advertising', 'Associate Degree', 2],
            ]],
        ];

        $limkokwingProgrammes = [];

        foreach ($catalogue as [$facultyCode, $facultyName, $departmentCode, $departmentName, $programmes]) {
            $faculty = Faculty::firstOrCreate(
                ['institution_id' => $limkokwing->id, 'code' => $facultyCode],
                ['name' => $facultyName, 'status' => 'active'],
            );

            $department = Department::firstOrCreate(
                ['institution_id' => $limkokwing->id, 'code' => $departmentCode],
                [
                    'faculty_id' => $faculty->id,
                    'name' => $departmentName,
                    'status' => 'active',
                ],
            );

            foreach ($programmes as [$programmeCode, $programmeName, $awardType, $durationYears]) {
                $limkokwingProgrammes[] = Programme::firstOrCreate(
                    ['institution_id' => $limkokwing->id, 'code' => $programmeCode],
                    [
                        'department_id' => $department->id,
                        'name' => $programmeName,
                        'award_type' => $awardType,
                        'duration_years' => $durationYears,
                        'entry_requirements' => 'Relevant secondary school qualifications and approved examination results.',
                        'status' => 'active',
                    ],
                );
            }
        }

        $cycle = AdmissionCycle::firstOrCreate(
            ['institution_id' => $limkokwing->id, 'slug' => '2027-intake'],
            [
                'academic_session_id' => $session->id,
                'name' => '2027 Admission Intake',
                'opens_at' => now()->subDay(),
                'closes_at' => now()->addMonths(6),
                'application_fee' => 250000,
                'status' => 'open',
            ],
        );

        $programmeIds = collect($limkokwingProgrammes)
            ->mapWithKeys(fn (Programme $programme) => [$programme->id => ['capacity' => 100]])
            ->all();

        $cycle->programmes()->syncWithoutDetaching($programmeIds);

        $documentTypes = [
            ['Passport Photograph', 'passport-photograph'],
            ['Birth Certificate', 'birth-certificate'],
            ['WASSCE Certificate', 'wassce-certificate'],
            ['Academic Transcript', 'academic-transcript'],
            ['National Identification Document', 'national-id'],
        ];

        foreach ($documentTypes as [$name, $slug]) {
            \App\Models\DocumentType::firstOrCreate(
                ['institution_id' => $limkokwing->id, 'slug' => $slug],
                ['name' => $name, 'allowed_extensions' => 'pdf,jpg,jpeg,png', 'max_file_size_kb' => 5120, 'status' => 'active'],
            );
        }

        foreach ($limkokwingProgrammes as $programme) {
            foreach (\App\Models\DocumentType::where('institution_id', $limkokwing->id)->get() as $documentType) {
                \App\Models\DocumentRequirement::firstOrCreate([
                    'institution_id' => $limkokwing->id,
                    'document_type_id' => $documentType->id,
                    'programme_id' => $programme->id,
                    'admission_cycle_id' => $cycle->id,
                ], ['is_required' => true, 'minimum_versions' => 1]);
            }
        }
    }
}
