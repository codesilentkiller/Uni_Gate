<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\AdmissionCycle;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\Programme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogueController extends Controller
{
    public function faculties(Institution $institution): View
    {
        $faculties = $institution->faculties()->withCount('departments')->latest()->get();
        return view('institution.catalogue.faculties', compact('institution','faculties'));
    }

    public function storeFaculty(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:30','unique:faculties,code,NULL,id,institution_id,'.$institution->id],
            'description' => ['nullable','string','max:5000'],
            'tuition_fee' => ['nullable','numeric','min:0'],
        ]);
        $institution->faculties()->create($validated + ['status'=>'active']);
        return back()->with('status','Faculty created.');
    }

    public function departments(Institution $institution): View
    {
        $departments = $institution->departments()->with(['faculty','programmes'])->latest()->get();
        $faculties = $institution->faculties()->where('status','active')->get();
        return view('institution.catalogue.departments', compact('institution','departments','faculties'));
    }

    public function storeDepartment(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'faculty_id' => ['required','exists:faculties,id'],
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:30','unique:departments,code,NULL,id,institution_id,'.$institution->id],
            'description' => ['nullable','string','max:5000'],
        ]);
        $faculty = $institution->faculties()->whereKey($validated['faculty_id'])->firstOrFail();
        $faculty->departments()->create($validated + ['institution_id'=>$institution->id,'status'=>'active']);
        return back()->with('status','Department created.');
    }

    public function programmes(Institution $institution): View
    {
        $programmes = $institution->programmes()->with(['department.faculty','admissionCycles'])->latest()->get();
        $departments = $institution->departments()->where('status','active')->get();
        return view('institution.catalogue.programmes', compact('institution','programmes','departments'));
    }

    public function storeProgramme(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:30','unique:programmes,code,NULL,id,institution_id,'.$institution->id],
            'award_type' => ['required','string','max:50'],
            'duration_years' => ['required','integer','min:1','max:10'],
            'description' => ['nullable','string','max:5000'],
            'entry_requirements' => ['nullable','string','max:5000'],
            'tuition_fee' => ['nullable','numeric','min:0'],
            'status' => ['required','in:draft,active,inactive'],
        ]);
        $department = $institution->departments()->whereKey($validated['department_id'])->firstOrFail();
        $department->programmes()->create($validated + ['institution_id'=>$institution->id]);
        return back()->with('status','Programme created.');
    }

    public function cycles(Institution $institution): View
    {
        $cycles = $institution->admissionCycles()->with(['academicSession','programmes'])->latest()->get();
        $sessions = AcademicSession::orderByDesc('starts_at')->get();
        $programmes = $institution->programmes()->where('status','active')->get();
        return view('institution.catalogue.cycles', compact('institution','cycles','sessions','programmes'));
    }

    public function storeCycle(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'academic_session_id' => ['required','exists:academic_sessions,id'],
            'name' => ['required','string','max:255'],
            'slug' => ['required','string','max:255','alpha_dash'],
            'opens_at' => ['required','date'],
            'closes_at' => ['required','date','after:opens_at'],
            'application_fee' => ['nullable','numeric','min:0'],
            'status' => ['required','in:draft,open,closed,suspended'],
            'programme_ids' => ['required','array','min:1'],
            'programme_ids.*' => ['integer','exists:programmes,id'],
        ]);

        $programmeIds = $validated['programme_ids'];
        unset($validated['programme_ids']);

        $cycle = $institution->admissionCycles()->create($validated + ['institution_id'=>$institution->id]);
        $validIds = $institution->programmes()->whereIn('id',$programmeIds)->pluck('id');
        $cycle->programmes()->sync($validIds);

        return back()->with('status','Admission cycle created.');
    }
}
