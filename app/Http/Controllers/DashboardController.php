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

        return view('dashboards.applicant', compact('user'));
    }
}
