<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AdmissionCycle;
use App\Models\Institution;
use App\Models\ApplicationDocument;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $applications = $request->user()->applicant?->applications()
            ->with(['institution', 'programme', 'admissionCycle'])
            ->latest()
            ->get() ?? collect();

        return view('applicant.applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Institution $institution, AdmissionCycle $cycle): View
    {
        abort_unless($institution->status === 'active', 404);
        abort_unless($cycle->institution_id === $institution->id, 404);
        abort_unless($cycle->isOpen(), 403, 'This admission cycle is not currently open.');

        $programmes = $cycle->programmes()->where('status', 'active')->get();

        return view('applicant.applications.create', compact('institution', 'cycle', 'programmes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institution_id' => ['required', 'exists:institutions,id'],
            'admission_cycle_id' => ['required', 'exists:admission_cycles,id'],
            'programme_id' => ['required', 'exists:programmes,id'],
            'entry_level' => ['nullable', 'string', 'max:50'],
            'personal_statement' => ['nullable', 'string', 'max:5000'],
            'date_of_birth' => ['nullable','date','before:today'],
            'gender' => ['nullable','in:male,female,other,prefer_not_to_say'],
            'nationality' => ['nullable','string','max:100'],
            'country_of_residence' => ['nullable','string','max:100'],
            'national_id' => ['nullable','string','max:100'],
            'wassce_index_number' => ['nullable','string','max:100'],
            'wassce_year' => ['nullable','digits:4'],
            'qualification_type' => ['required','in:WASSCE,GCSCE'],
            'qualification_index_number' => ['nullable','required_with:qualification_type','string','max:100'],
            'qualification_year' => ['nullable','required_with:qualification_type','digits:4'],
            'subjects' => ['nullable','array','max:12'],
            'subjects.*.subject' => ['nullable','string','max:100'],
            'subjects.*.grade' => ['nullable','in:A1,B2,B3,C4,C5,C6,D7,E8,F9'],
            'wassce_certificate' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'id_card' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);

        $institution = Institution::whereKey($validated['institution_id'])->where('status', 'active')->firstOrFail();
        $cycle = AdmissionCycle::whereKey($validated['admission_cycle_id'])->where('institution_id', $institution->id)->firstOrFail();

        abort_unless($cycle->isOpen(), 403, 'This admission cycle is not currently open.');

        $programme = $cycle->programmes()
            ->whereKey($validated['programme_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $applicant = $request->user()->applicant;
        abort_if(! $applicant, 403, 'Applicant profile is required.');

        $application = DB::transaction(function () use ($request, $applicant, $institution, $cycle, $programme, $validated) {
            $existing = Application::where([
                'applicant_id' => $applicant->id,
                'institution_id' => $institution->id,
                'programme_id' => $programme->id,
                'admission_cycle_id' => $cycle->id,
            ])->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'programme_id' => 'You already have an application for this programme and admission cycle.',
                ]);
            }

            $application = Application::create([
                'application_reference' => $this->generateReference($institution, $cycle),
                'applicant_id' => $applicant->id,
                'institution_id' => $institution->id,
                'programme_id' => $programme->id,
                'admission_cycle_id' => $cycle->id,
                'status' => 'draft',
                'entry_level' => $validated['entry_level'] ?? null,
                'personal_statement' => $validated['personal_statement'] ?? null,
            ]);

            $applicant->update(array_filter([
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'country_of_residence' => $validated['country_of_residence'] ?? null,
                'national_id' => $validated['national_id'] ?? null,
            ], fn ($value) => $value !== null));

            foreach ($validated['subjects'] ?? [] as $subject) {
                if (! empty($subject['subject']) && ! empty($subject['grade'])) {
                    $qualificationType = $validated['qualification_type'];
                    $applicant->examinationResults()->updateOrCreate([
                        'examination_type' => $qualificationType,
                        'examination_year' => $validated['qualification_year'],
                        'index_number' => $validated['qualification_index_number'],
                        'subject' => $subject['subject'],
                    ], [
                        'grade' => $subject['grade'],
                        'verification_status' => 'pending',
                    ]);
                }
            }

            $uploads = [
                'wassce_certificate' => 'wassce-certificate',
                'id_card' => 'national-id',
            ];

            foreach ($uploads as $field => $documentSlug) {
                $file = $request->file($field);
                $documentType = DocumentType::firstOrCreate(
                    ['institution_id' => $institution->id, 'slug' => $documentSlug],
                    [
                        'name' => $documentSlug === 'wassce-certificate'
                            ? $validated['qualification_type'].' Result / Certificate'
                            : 'National Identification Document',
                        'allowed_extensions' => 'pdf,jpg,jpeg,png',
                        'max_file_size_kb' => 5120,
                        'status' => 'active',
                    ],
                );

                $extension = strtolower($file->getClientOriginalExtension());
                $folder = 'institutions/'.$institution->slug.'/'.$application->application_reference;
                Storage::disk('unigate-private')->makeDirectory($folder);

                $path = $folder.'/'.$documentSlug.'-v1.'.$extension;
                $stored = $file->storeAs($folder, basename($path), 'unigate-private');
                abort_unless($stored, 500, 'The document could not be stored.');

                $document = ApplicationDocument::create([
                    'application_id' => $application->id,
                    'institution_id' => $institution->id,
                    'applicant_id' => $applicant->id,
                    'document_type_id' => $documentType->id,
                    'uploaded_by' => $applicant->user_id,
                    'disk' => 'unigate-private',
                    'stored_path' => $stored,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'checksum' => hash_file('sha256', $file->getRealPath()),
                    'version' => 1,
                    'status' => 'uploaded',
                ]);

                $document->verificationHistory()->create([
                    'institution_id' => $institution->id,
                    'reviewed_by' => $applicant->user_id,
                    'from_status' => null,
                    'to_status' => 'uploaded',
                    'notes' => 'Required document uploaded with application form.',
                    'reviewed_at' => now(),
                ]);
            }

            $application->statusHistory()->create([
                'from_status' => null,
                'to_status' => 'draft',
                'changed_by' => $applicant->user_id,
                'notes' => 'Draft application created.',
                'changed_at' => now(),
            ]);

            return $application;
        });

        return redirect()->route('applicant.applications.show', $application)
            ->with('status', 'Application draft created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application): View
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);
        $application->load(['institution', 'programme.department', 'admissionCycle', 'statusHistory.changedBy']);

        return view('applicant.applications.show', compact('application'));
    }

    /**
     * Submit the specified resource.
     */
    public function submit(Application $application): RedirectResponse
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);
        abort_unless($application->status === 'draft', 422, 'Only draft applications can be submitted.');

        if (! $application->admissionCycle->isOpen()) {
            abort(403, 'The admission cycle is no longer open.');
        }

        $application->transitionTo('submitted', auth()->user(), 'Application submitted by applicant.');

        return back()->with('status', 'Application submitted successfully.');
    }

    /**
     * Withdraw the specified resource.
     */
    public function withdraw(Application $application): RedirectResponse
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);

        if (! $application->transitionTo('withdrawn', auth()->user(), 'Application withdrawn by applicant.')) {
            abort(422, 'This application can no longer be withdrawn.');
        }

        return back()->with('status', 'Application withdrawn.');
    }

    /**
     * Display the applicant acceptance letter.
     */
    public function acceptanceLetter(Application $application): View
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);
        abort_unless($application->status === 'accepted' && $application->student_id, 404, 'Acceptance letter is not available yet.');

        $application->load(['institution','programme.department.faculty','admissionCycle.academicSession','applicant.user']);

        return view('applicant.applications.acceptance-letter', compact('application'));
    }

    public function edit(Application $application): \Illuminate\View\View
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);
        abort_unless(in_array($application->status, ['draft', 'documents_pending', 'under_review'], true), 422, 'This application cannot be edited now.');

        $application->load(['institution', 'programme.department', 'admissionCycle']);
        return view('applicant.applications.edit', compact('application'));
    }

    public function update(\Illuminate\Http\Request $request, Application $application): RedirectResponse
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);
        abort_unless(in_array($application->status, ['draft', 'documents_pending', 'under_review'], true), 422, 'This application cannot be edited now.');

        $validated = $request->validate([
            'entry_level' => ['nullable', 'string', 'max:50'],
            'personal_statement' => ['nullable', 'string', 'max:5000'],
        ]);

        $application->update($validated);

        if ($application->correction_requested_at) {
            $application->update([
                'correction_notes' => null,
                'correction_requested_at' => null,
            ]);
            $application->transitionTo('under_review', auth()->user(), 'Applicant submitted the requested application corrections.');
        }

        return redirect()->route('applicant.applications.show', $application)
            ->with('status', 'Application corrections saved successfully.');
    }

    public function confirmEnrollment(Application $application): RedirectResponse
    {
        abort_unless(auth()->user()->applicant?->is($application->applicant), 403);
        abort_unless($application->status === 'accepted' && $application->enrolled_at, 422, 'Enrollment must be completed by the university before confirmation.');

        $application->update(['applicant_confirmed_enrollment_at' => now()]);
        return back()->with('status', 'Enrollment confirmed successfully.');
    }

    private function generateReference(Institution $institution, AdmissionCycle $cycle): string
    {
        $number = Application::withTrashed()->where('institution_id', $institution->id)->count() + 1;

        $year = $cycle->academicSession->starts_at->format('Y');

        return 'UG-'.$institution->institution_code.'-'.$year.'-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}
