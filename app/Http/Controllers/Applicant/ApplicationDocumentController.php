<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadApplicationDocumentRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationDocumentController extends Controller
{
    public function index(Application $application)
    {
        abort_unless(auth()->user()->applicant?->id === $application->applicant_id, 403);

        $documents = $application->documents()->with('documentType')->latest()->get();
        $documentTypes = DocumentType::query()
            ->where(function ($query) use ($application) {
                $query->whereNull('institution_id')
                    ->orWhere('institution_id', $application->institution_id);
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('applicant.applications.documents', compact('application', 'documents', 'documentTypes'));
    }

    public function store(UploadApplicationDocumentRequest $request, Application $application): RedirectResponse
    {
        abort_unless(auth()->user()->applicant?->id === $application->applicant_id, 403);
        abort_unless(in_array($application->status, ['draft', 'documents_pending'], true), 422, 'Documents cannot be added to this application status.');

        $documentType = DocumentType::whereKey($request->validated('document_type_id'))
            ->where(function ($query) use ($application) {
                $query->whereNull('institution_id')
                    ->orWhere('institution_id', $application->institution_id);
            })
            ->where('status', 'active')
            ->firstOrFail();

        $latest = $application->documents()->where('document_type_id', $documentType->id)->latest('version')->first();

        if ($latest && $latest->status === 'replacement_required') {
            abort_if($latest->replacement_deadline && now()->gt($latest->replacement_deadline), 422, 'The document replacement deadline has passed.');
        }

        $version = $latest ? $latest->version + 1 : 1;

        $file = $request->file('document');
        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = array_filter(array_map('trim', explode(',', $documentType->allowed_extensions)));

        abort_unless(in_array($extension, $allowed, true), 422, 'This file type is not allowed for the selected document.');

        $disk = env('UNIGATE_DOCUMENT_DISK', 'unigate-private');
        $path = sprintf(
            'institutions/%d/applications/%d/%s-v%d.%s',
            $application->institution_id,
            $application->id,
            $documentType->slug,
            $version,
            $extension,
        );

        $stored = $file->storeAs(dirname($path), basename($path), $disk);
        abort_unless($stored, 500, 'The document could not be stored.');

        $document = ApplicationDocument::create([
            'application_id' => $application->id,
            'institution_id' => $application->institution_id,
            'applicant_id' => $application->applicant_id,
            'document_type_id' => $documentType->id,
            'uploaded_by' => auth()->id(),
            'disk' => $disk,
            'stored_path' => $stored,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'version' => $version,
            'status' => 'uploaded',
        ]);

        $document->verificationHistory()->create([
            'institution_id' => $application->institution_id,
            'reviewed_by' => auth()->id(),
            'from_status' => null,
            'to_status' => 'uploaded',
            'notes' => 'Document uploaded by applicant.',
            'reviewed_at' => now(),
        ]);

        return redirect()->route('applicant.applications.documents.index', $application)
            ->with('status', 'Document uploaded successfully. Your document list and version have been updated.');
    }

    public function download(ApplicationDocument $document): StreamedResponse
    {
        $application = $document->application;
        abort_unless(auth()->user()->applicant?->id === $application->applicant_id, 403);
        abort_unless(Storage::disk($document->disk)->exists($document->stored_path), 404);

        return Storage::disk($document->disk)->download($document->stored_path, $document->original_name, ['X-Content-Type-Options' => 'nosniff']);
    }

    public function dashboard(): \Illuminate\View\View
    {
        $applicant = auth()->user()->applicant;
        abort_if(! $applicant, 403, 'Applicant profile is required.');

        $applications = Application::with([
            'institution',
            'programme',
            'documents.documentType',
        ])
            ->where('applicant_id', $applicant->id)
            ->latest()
            ->get();

        $documents = $applications
            ->flatMap(fn (Application $application) => $application->documents)
            ->sortByDesc('created_at');

        return view('applicant.documents', compact('applications', 'documents'));
    }
}
