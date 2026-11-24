<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Application {{ $application->application_reference }}</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl p-8">
        <div class="mb-6 flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500">{{ $application->institution->name }}</p>
                <h1 class="mt-1 text-3xl font-bold">{{ $application->application_reference }}</h1>
            </div>
            <span class="rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">
                {{ str_replace('_',' ',ucwords($application->status)) }}
            </span>
        </div>

        @if(session('status'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <section class="rounded-2xl border bg-white p-6">
                <h2 class="text-lg font-bold">Application details</h2>

                <dl class="mt-5 grid grid-cols-2 gap-5 text-sm">
                    <div>
                        <dt class="text-slate-500">Programme</dt>
                        <dd class="mt-1 font-semibold">{{ $application->programme->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Admission cycle</dt>
                        <dd class="mt-1 font-semibold">{{ $application->admissionCycle->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Entry level</dt>
                        <dd class="mt-1 font-semibold">{{ $application->entry_level ?: 'Not specified' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Submitted</dt>
                        <dd class="mt-1 font-semibold">{{ $application->submitted_at?->format('d M Y') ?: 'Not submitted' }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <p class="text-sm text-slate-500">Personal statement</p>
                    <p class="mt-2 leading-7">{{ $application->personal_statement ?: 'No personal statement provided.' }}</p>
                </div>

                @if($application->correction_notes)
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4"><p class="text-xs font-bold uppercase text-amber-700">Corrections requested</p><p class="mt-2 text-sm">{{ $application->correction_notes }}</p></div>
                @endif
                @if(in_array($application->status,['draft','documents_pending','under_review'],true) && ($application->status === 'draft' || $application->correction_notes))
                    <a href="{{ route('applicant.applications.edit',$application) }}" class="mt-4 block w-full rounded-lg border px-4 py-3 text-center text-sm font-semibold text-blue-700">Edit application</a>
                @endif
            </section>

            <aside class="space-y-4">
                <div class="rounded-2xl border bg-white p-6">
                    <h2 class="font-bold">Actions</h2>

                    @if($application->status === 'draft')
                        <form method="POST" action="{{ route('applicant.applications.submit',$application) }}" class="mt-4">
                            @csrf
                            <button class="w-full rounded-lg bg-blue-700 py-3 text-sm font-semibold text-white">
                                Submit application
                            </button>
                        </form>
                    @endif

                    @if(in_array($application->status,['draft','submitted','under_review','documents_pending','awaiting_payment','approved'],true))
                        <form method="POST" action="{{ route('applicant.applications.withdraw',$application) }}" class="mt-3">
                            @csrf
                            <button class="w-full rounded-lg border border-red-200 py-3 text-sm font-semibold text-red-700">
                                Withdraw application
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('applicant.applications.documents.index',$application) }}" class="mt-4 block w-full rounded-lg border px-4 py-3 text-center text-sm font-semibold text-blue-700">
                        Manage documents
                    </a>
                </div>

                <div class="rounded-2xl border bg-white p-6">
                    <h2 class="font-bold">Status history</h2>

                    <div class="mt-4 space-y-4">
                        @foreach($application->statusHistory as $history)
                            <div class="border-l-2 border-blue-200 pl-4">
                                <p class="text-sm font-semibold">{{ str_replace('_',' ',ucwords($history->to_status)) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $history->changed_at->format('d M Y H:i') }}</p>

                                @if($history->notes)
                                    <p class="mt-1 text-xs text-slate-500">{{ $history->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>

        @if($application->status === 'accepted' && $application->student_id)
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-bold uppercase text-emerald-700">Accepted student</p>
                <p class="mt-1 font-bold">{{ $application->student_id }}</p>
                <a href="{{ route('applicant.applications.acceptance-letter', $application) }}" class="mt-3 inline-block text-sm font-semibold text-emerald-800">View acceptance letter</a>
            </div>
        @endif

        @if($application->enrolled_at && ! $application->applicant_confirmed_enrollment_at)
            <form method="POST" action="{{ route('applicant.applications.confirm-enrollment',$application) }}" class="mt-3">@csrf @method('PATCH')<button class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Confirm enrollment</button></form>
        @elseif($application->applicant_confirmed_enrollment_at)
            <p class="mt-3 text-sm font-bold text-emerald-800">Enrollment confirmed {{ $application->applicant_confirmed_enrollment_at->format('d M Y') }}</p>
        @endif
    </div>
</x-app-layout>
