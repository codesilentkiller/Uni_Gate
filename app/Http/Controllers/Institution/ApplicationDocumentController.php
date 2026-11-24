<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyApplicationDocumentRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationDocumentController extends Controller
{
    public function index(Institution $institution, Application $application)
    {
        abort_unless($application->institution_id === $institution->id, 404);

        $documents = $application->documents()->with(['documentType', 'uploader', 'verificationHistory.reviewer'])->latest()->get();

        return view('institution.application-documents', compact('institution', 'application', 'documents'));
    }

    public function verify(VerifyApplicationDocumentRequest $request, Institution $institution, ApplicationDocument $document)
    {
        abort_unless($document->institution_id === $institution->id, 404);

        abort_unless(auth()->user()->institutions()->whereKey($institution->id)->wherePivot('status', 'active')->exists(), 403);

        $validated = $request->validated();

        DB::transaction(function () use ($document, $institution, $validated) {
            $from = $document->status;

            $document->update([
                'status' => $validated['status'],
                'verification_notes' => $validated['notes'] ?? null,
                'verified_at' => now(),
                'replacement_deadline' => $validated['replacement_deadline'] ?? null,
            ]);

            $document->verificationHistory()->create([
                'institution_id' => $institution->id,
                'reviewed_by' => auth()->id(),
                'from_status' => $from,
                'to_status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'reviewed_at' => now(),
            ]);

            if ($validated['status'] !== 'verified') {
                $document->application->transitionTo('documents_pending', auth()->user(), 'Application documents require review.');
            }
        });

        return back()->with('status', 'Document verification updated.');
    }

    public function download(Institution $institution, ApplicationDocument $document): StreamedResponse
    {
        abort_unless($document->institution_id === $institution->id, 404);
        abort_unless(auth()->user()->institutions()->whereKey($institution->id)->wherePivot('status', 'active')->exists(), 403);
        abort_unless(Storage::disk($document->disk)->exists($document->stored_path), 404);

        return Storage::disk($document->disk)->download($document->stored_path, $document->original_name, ['X-Content-Type-Options' => 'nosniff']);
    }
}
