<?php

use App\Http\Controllers\Admission\AdmissionDecisionController;
use App\Http\Controllers\Admission\ApplicationReviewController;
use App\Http\Controllers\Applicant\ApplicationDocumentController as ApplicantApplicationDocumentController;
use App\Http\Controllers\Applicant\ApplicationController as ApplicantApplicationController;
use App\Http\Controllers\Applicant\AdmissionOfferController as ApplicantAdmissionOfferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Institution\ApplicationDocumentController as InstitutionApplicationDocumentController;
use App\Http\Controllers\Institution\ApplicationController as InstitutionApplicationController;
use App\Http\Controllers\Institution\AdmissionOfferController as InstitutionAdmissionOfferController;
use App\Http\Controllers\Institution\CatalogueController;
use App\Http\Controllers\Institution\ProfileController as InstitutionProfileController;
use App\Http\Controllers\Institution\ReportController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\InstitutionLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Platform\DashboardController as PlatformDashboardController;
use App\Http\Controllers\Platform\InstitutionController as PlatformInstitutionController;
use App\Http\Controllers\Platform\UserController as PlatformUserController;
use App\Http\Controllers\Platform\RoleController as PlatformRoleController;
use App\Models\Institution;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Institution\StaffController;

Route::view('/', 'welcome')->name('home');
Route::get('/universities', [InstitutionController::class, 'index'])->name('universities.index');
Route::get('/universities/{institution:slug}', [InstitutionController::class, 'show'])->name('universities.show');
Route::get('/institution/{institution:slug}/login', [InstitutionLoginController::class, 'create'])->name('institution.login');

Route::get('/platform/login', fn () => redirect()->route('login'))->name('platform.login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/institution/{institution:slug}/dashboard', function (Institution $institution) {
        return view('dashboards.institution', [
            'institution' => $institution,
            'user' => auth()->user(),
            'role' => auth()->user()->roles()->wherePivot('institution_id', $institution->id)->first(),
        ]);
    })->middleware('institution')->name('institution.dashboard');

    Route::get('/institution/{institution:slug}/applications', [InstitutionApplicationController::class, 'index'])
        ->middleware(['institution', 'role:admissions-officer,university-administrator'])
        ->name('institution.applications');

    Route::get('/institution/{institution:slug}/applications/{application}', [InstitutionApplicationController::class, 'show'])
        ->middleware(['institution', 'role:admissions-officer,university-administrator'])
        ->name('institution.applications.show');

    Route::patch('/institution/{institution:slug}/applications/{application}/status', [InstitutionApplicationController::class, 'updateStatus'])
        ->middleware(['institution', 'role:admissions-officer,university-administrator'])
        ->name('institution.applications.status');

    Route::get('/applications', [ApplicantApplicationController::class, 'index'])
        ->name('applicant.applications.index');

    Route::get('/universities/{institution:slug}/cycles/{cycle}/apply', [ApplicantApplicationController::class, 'create'])
        ->name('applicant.applications.create');

    Route::post('/applications', [ApplicantApplicationController::class, 'store'])
        ->name('applicant.applications.store');

    Route::get('/applications/{application}', [ApplicantApplicationController::class, 'show'])
        ->name('applicant.applications.show');

    Route::post('/applications/{application}/submit', [ApplicantApplicationController::class, 'submit'])
        ->name('applicant.applications.submit');

    Route::post('/applications/{application}/withdraw', [ApplicantApplicationController::class, 'withdraw'])
        ->name('applicant.applications.withdraw');

    Route::get('/institution/{institution:slug}/staff', [StaffController::class, 'index'])
        ->middleware(['institution', 'role:university-administrator'])
        ->name('institution.staff');

    Route::post('/institution/{institution:slug}/staff', [StaffController::class, 'store'])
        ->middleware(['institution', 'role:university-administrator'])
        ->name('institution.staff.store');

    Route::get('/institution/{institution:slug}/students', function (Institution $institution) {
        return view('institution.students', compact('institution'));
    })->middleware(['institution', 'role:registrar,university-administrator'])->name('institution.students');

    Route::get('/institution/{institution:slug}/finance', function (Institution $institution) {
        return view('institution.finance', compact('institution'));
    })->middleware(['institution', 'role:finance-officer,university-administrator'])->name('institution.finance');

    Route::get('/applications/{application}/documents', [ApplicantApplicationDocumentController::class, 'index'])
        ->name('applicant.applications.documents.index');

    Route::post('/applications/{application}/documents', [ApplicantApplicationDocumentController::class, 'store'])
        ->name('applicant.applications.documents.store');

    Route::get('/application-documents/{document}/download', [ApplicantApplicationDocumentController::class, 'download'])
        ->name('applicant.application-documents.download');

    Route::get('/institution/{institution:slug}/applications/{application}/documents', [InstitutionApplicationDocumentController::class, 'index'])
        ->middleware(['institution', 'role:admissions-officer,university-administrator,document-verification-officer,read-only-staff,registrar'])
        ->name('institution.applications.documents');

    Route::post('/institution/{institution:slug}/documents/{document}/verify', [InstitutionApplicationDocumentController::class, 'verify'])
        ->middleware(['institution', 'role:admissions-officer,university-administrator,document-verification-officer'])
        ->name('institution.application-documents.verify');

    Route::get('/institution/{institution:slug}/application-documents/{document}/download', [InstitutionApplicationDocumentController::class, 'download'])
        ->middleware(['institution', 'role:admissions-officer,university-administrator,document-verification-officer,read-only-staff,registrar'])
        ->name('institution.application-documents.download');

    Route::post('/institution/{institution:slug}/applications/{application}/reviews', [ApplicationReviewController::class, 'store'])
        ->middleware(['institution','role:university-administrator,admissions-officer,department-faculty-officer'])
        ->name('institution.applications.reviews.store');

    Route::post('/institution/{institution:slug}/applications/{application}/decision', [AdmissionDecisionController::class, 'store'])
        ->middleware(['institution','role:university-administrator,admissions-officer'])
        ->name('institution.applications.decision');

    Route::get('/institution/{institution:slug}/offers', [InstitutionAdmissionOfferController::class, 'index'])
        ->middleware(['institution','role:university-administrator,admissions-officer,registrar,read-only-staff'])
        ->name('institution.admission-offers.index');

    Route::post('/institution/{institution:slug}/applications/{application}/offers', [InstitutionAdmissionOfferController::class, 'store'])
        ->middleware(['institution','role:university-administrator,admissions-officer'])
        ->name('institution.applications.offers.store');

    Route::post('/institution/{institution:slug}/offers/{offer}/withdraw', [InstitutionAdmissionOfferController::class, 'withdraw'])
        ->middleware(['institution','role:university-administrator,admissions-officer'])
        ->name('institution.admission-offers.withdraw');

    Route::get('/admission-offers', [ApplicantAdmissionOfferController::class, 'index'])
        ->name('applicant.admission-offers.index');

    Route::post('/admission-offers/{offer}/respond', [ApplicantAdmissionOfferController::class, 'respond'])
        ->name('applicant.admission-offers.respond');

    Route::get('/applicant/payments', fn () => view('applicant.payments'))->name('applicant.payments');

    Route::get('/institution/{institution:slug}/profile', [InstitutionProfileController::class,'edit'])
        ->middleware(['institution','role:university-administrator'])
        ->name('institution.profile.edit');

    Route::patch('/institution/{institution:slug}/profile', [InstitutionProfileController::class,'update'])
        ->middleware(['institution','role:university-administrator'])
        ->name('institution.profile.update');

    Route::get('/institution/{institution:slug}/faculties', [CatalogueController::class,'faculties'])
        ->middleware(['institution','role:university-administrator,admissions-officer,department-faculty-officer'])
        ->name('institution.faculties');
    Route::post('/institution/{institution:slug}/faculties', [CatalogueController::class,'storeFaculty'])
        ->middleware(['institution','role:university-administrator'])
        ->name('institution.faculties.store');

    Route::get('/institution/{institution:slug}/departments', [CatalogueController::class,'departments'])
        ->middleware(['institution','role:university-administrator,admissions-officer,department-faculty-officer'])
        ->name('institution.departments');
    Route::post('/institution/{institution:slug}/departments', [CatalogueController::class,'storeDepartment'])
        ->middleware(['institution','role:university-administrator'])
        ->name('institution.departments.store');

    Route::get('/institution/{institution:slug}/programmes', [CatalogueController::class,'programmes'])
        ->middleware(['institution','role:university-administrator,admissions-officer,department-faculty-officer'])
        ->name('institution.programmes');
    Route::post('/institution/{institution:slug}/programmes', [CatalogueController::class,'storeProgramme'])
        ->middleware(['institution','role:university-administrator'])
        ->name('institution.programmes.store');

    Route::get('/institution/{institution:slug}/cycles', [CatalogueController::class,'cycles'])
        ->middleware(['institution','role:university-administrator,admissions-officer'])
        ->name('institution.cycles');
    Route::post('/institution/{institution:slug}/cycles', [CatalogueController::class,'storeCycle'])
        ->middleware(['institution','role:university-administrator'])
        ->name('institution.cycles.store');

    Route::get('/institution/{institution:slug}/reports', [ReportController::class,'index'])
        ->middleware(['institution','role:university-administrator,admissions-officer,registrar,read-only-staff'])
        ->name('institution.reports');

    Route::get('/applications/{application}/acceptance-letter', [ApplicantApplicationController::class, 'acceptanceLetter'])
        ->name('applicant.applications.acceptance-letter');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','verified','role:super-administrator,platform-support-administrator,platform-auditor'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/overview', [PlatformDashboardController::class,'index'])->name('dashboard');

    Route::get('/universities', [PlatformInstitutionController::class,'index'])->name('institutions.index');
    Route::get('/universities/create', [PlatformInstitutionController::class,'create'])->name('institutions.create');
    Route::post('/universities', [PlatformInstitutionController::class,'store'])
        ->middleware('role:super-administrator')->name('institutions.store');
    Route::get('/universities/{institution:slug}', [PlatformInstitutionController::class,'show'])->name('institutions.show');
    Route::patch('/universities/{institution:slug}/status', [PlatformInstitutionController::class,'update'])
        ->middleware('role:super-administrator')->name('institutions.status');

    Route::get('/users', [PlatformUserController::class,'index'])->name('users.index');
    Route::get('/users/create', [PlatformUserController::class,'create'])
        ->middleware('role:super-administrator')->name('users.create');
    Route::post('/users', [PlatformUserController::class,'store'])
        ->middleware('role:super-administrator')->name('users.store');

    Route::get('/roles', [PlatformRoleController::class,'index'])->name('roles.index');

    Route::get('/reports', fn () => view('platform.reports'))->name('reports');
    Route::get('/audit-logs', fn () => view('platform.audit-logs'))->name('audit-logs');
    Route::get('/settings', fn () => view('platform.settings'))->name('settings');
});

require __DIR__.'/auth.php';
