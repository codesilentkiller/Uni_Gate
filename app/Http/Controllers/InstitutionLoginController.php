<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\View\View;

class InstitutionLoginController extends Controller
{
    public function create(Institution $institution): View
    {
        abort_unless($institution->status === 'active', 404);

        if (auth()->check()) {
            $isInstitutionStaff = auth()->user()->roles()
                ->whereIn('roles.slug', [
                    'university-administrator',
                    'admissions-officer',
                    'registrar',
                    'finance-officer',
                    'department-faculty-officer',
                    'document-verification-officer',
                    'read-only-staff',
                ])
                ->exists();

            abort_unless($isInstitutionStaff, 403, 'Institution staff access is restricted.');
        }

        return view('auth.institution-login', compact('institution'));
    }
}
