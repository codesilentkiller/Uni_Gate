<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        $role = $user->roles()->withPivot('institution_id')->first();

        if ($role && in_array($role->slug, ['super-administrator', 'platform-support-administrator', 'platform-auditor'], true)) {
            return view('dashboards.platform', compact('user'));
        }

        if ($role && in_array($role->slug, ['university-administrator', 'admissions-officer', 'registrar', 'finance-officer', 'department-faculty-officer', 'document-verification-officer', 'read-only-staff'], true)) {
            $institution = $user->institutions()->wherePivot('status', 'active')->first();

            if ($institution) {
                return view('dashboards.institution', compact('user', 'institution', 'role'));
            }

            return view('dashboards.no-institution', compact('user'));
        }

        $applicant = $user->applicant;

        $dashboardStats = [
            'applications' => $applicant ? $applicant->applications()->count() : 0,
            'submitted' => $applicant ? $applicant->applications()->whereIn('status', ['submitted', 'under_review', 'documents_pending', 'awaiting_payment', 'approved', 'offered', 'accepted', 'rejected'])->count() : 0,
            'offers' => $applicant ? \App\Models\AdmissionOffer::whereHas('application', fn ($query) => $query->where('applicant_id', $applicant->id))->count() : 0,
            'registrations' => $applicant ? $applicant->applications()->whereNotNull('student_id')->count() : 0,
            'enrolled' => $applicant ? $applicant->applications()->whereNotNull('enrolled_at')->count() : 0,
        ];

        $recentApplications = $applicant
            ? $applicant->applications()->with(['institution', 'programme', 'admissionCycle'])->latest()->limit(5)->get()
            : collect();

        return view('dashboards.applicant', compact('user', 'dashboardStats', 'recentApplications'));
    }
}
