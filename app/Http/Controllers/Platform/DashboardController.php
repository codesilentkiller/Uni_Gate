<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Institution;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'institutions' => Institution::count(),
            'activeInstitutions' => Institution::where('status','active')->count(),
            'users' => User::count(),
            'applicants' => Applicant::count(),
            'applications' => Application::count(),
            'submittedApplications' => Application::where('status','submitted')->count(),
        ];

        $institutions = Institution::latest()->take(5)->get();

        return view('platform.dashboard', compact('stats','institutions'));
    }
}
