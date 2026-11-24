<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\ExaminationResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExaminationResultController extends Controller
{
    public function update(Request $request, ExaminationResult $result): RedirectResponse
    {
        abort_unless(auth()->user()->applicant?->is($result->applicant), 403);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:100'],
            'grade' => ['required', 'in:A1,B2,B3,C4,C5,C6,D7,E8,F9'],
        ]);

        abort_if($result->verification_status === 'verified', 422, 'Verified examination results cannot be changed.');

        $result->update($validated + ['verification_status' => 'pending']);

        return back()->with('status', 'Examination subject and grade updated successfully.');
    }
}
