<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Institution $institution): View
    {
        $applications = $institution->applications();

        $stats = [
            'total' => (clone $applications)->count(),
            'submitted' => (clone $applications)->where('status','submitted')->count(),
            'approved' => (clone $applications)->where('status','approved')->count(),
            'offered' => (clone $applications)->where('status','offered')->count(),
            'accepted' => (clone $applications)->where('status','accepted')->count(),
            'rejected' => (clone $applications)->where('status','rejected')->count(),
        ];

        $applicationsByStatus = (clone $applications)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total','status');

        return view('institution.reports', compact('institution','stats','applicationsByStatus'));
    }
}
