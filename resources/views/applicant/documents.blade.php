<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Documents</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">Applicant document centre</p>
                <h1 class="mt-1 text-2xl font-bold">My Documents</h1>
            </div>
            <a href="{{ route('applicant.applications.index') }}" class="rounded-lg border px-4 py-2.5 text-sm font-semibold text-slate-700">Manage applications</a>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
            <section class="overflow-hidden rounded-2xl border bg-white">
                <div class="border-b px-6 py-4">
                    <h2 class="font-bold">Uploaded documents</h2>
                </div>
                <div class="divide-y">
                    @forelse($documents as $document)
                        <article class="flex items-center justify-between gap-4 px-6 py-5">
                            <div class="min-w-0">
                                <p class="font-semibold">{{ $document->documentType->name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">
                                    {{ $document->original_name }} · v{{ $document->version }} · {{ number_format($document->size_bytes / 1024, 1) }} KB
                                </p>
                                <p class="mt-1 text-xs text-slate-500">{{ $document->application->application_reference }}</p>
                                @if($document->status === 'replacement_required' && $document->replacement_deadline)
                                    <p class="mt-2 text-xs font-bold text-amber-700">Replacement due {{ $document->replacement_deadline->format('d M Y') }}</p>
                                @endif
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold">{{ str_replace('_', ' ', ucwords($document->status)) }}</span>
                                <a href="{{ route('applicant.application-documents.download', $document) }}" class="text-sm font-semibold text-blue-700">Download</a>
                            </div>
                        </article>
                    @empty
                        <p class="py-16 text-center text-sm text-slate-500">No documents have been uploaded yet.</p>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-4">
                @forelse($applications as $application)
                    <section class="rounded-2xl border bg-white p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">{{ $application->institution->institution_code }}</p>
                        <h2 class="mt-2 font-bold">{{ $application->programme->name }}</h2>
                        <p class="mt-1 text-xs text-slate-500">{{ $application->application_reference }}</p>
                        <p class="mt-3 text-sm text-slate-600">{{ $application->documents->count() }} uploaded {{ Str::plural('document', $application->documents->count()) }}</p>
                        <a href="{{ route('applicant.applications.documents.index', $application) }}" class="mt-4 inline-block rounded-lg border px-4 py-2 text-sm font-semibold text-blue-700">Upload or manage</a>
                    </section>
                @empty
                    <section class="rounded-2xl border border-dashed bg-white p-6 text-center">
                        <p class="text-sm text-slate-500">Create an application to begin uploading documents.</p>
                        <a href="{{ route('universities.index') }}" class="mt-4 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Explore universities</a>
                    </section>
                @endforelse
            </aside>
        </div>
    </div>
</x-app-layout>
