<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Admission Offers</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl p-8">
        <a href="{{ route('dashboard') }}" class="mb-5 inline-flex text-sm font-semibold text-slate-600">← Back to applicant dashboard</a>
        <div class="mb-6">
            <p class="text-sm text-slate-500">Applicant admission offers</p>
            <h1 class="text-2xl font-bold">My Admission Offers</h1>
        </div>
        @if(session('status'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif
        <div class="space-y-4">
            @forelse($offers as $offer)
                <article class="rounded-2xl border bg-white p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-blue-700">{{ $offer->institution->institution_code }}</p>
                            <h2 class="mt-2 text-xl font-bold">{{ $offer->application->programme->name }}</h2>
                            <p class="mt-2 text-sm text-slate-500">Offer reference: {{ $offer->offer_reference }}</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ str_replace('_',' ',ucwords($offer->offer_status)) }}</span>
                    </div>
                    @if($offer->offer_message)
                        <p class="mt-5 leading-7 text-slate-600">{{ $offer->offer_message }}</p>
                    @endif
                    @if($offer->offer_status === 'accepted' && $offer->application->student_id)
                        @php
                            $tuitionFee = $offer->application->programme->tuition_fee
                                ?: $offer->application->programme->department->faculty->tuition_fee
                                ?: 0;
                        @endphp
                        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Student registration</p>
                            <p class="mt-2 text-xl font-bold">Student ID: {{ $offer->application->student_id }}</p>
                            <p class="mt-2 text-sm">Tuition fee: <strong>Le {{ number_format((float) $tuitionFee, 2) }}</strong></p>
                            @if($offer->application->enrolled_at)
                                <p class="mt-2 text-sm font-bold text-emerald-800">Enrolled on {{ $offer->application->enrolled_at->format('d M Y') }}</p>
                            @endif
                            <a href="{{ route('applicant.applications.acceptance-letter', $offer->application) }}" class="mt-4 inline-block rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white">View & print acceptance letter</a>
                        </div>
                    @endif

                    @if($offer->offer_status === 'issued')
                        <form method="POST" action="{{ route('applicant.admission-offers.respond',$offer) }}" class="mt-6 flex gap-3">
                            @csrf
                            <select name="response" class="rounded-lg border-slate-300 text-sm">
                                <option value="accepted">Accept offer</option>
                                <option value="declined">Decline offer</option>
                            </select>
                            <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white">Submit response</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border bg-white py-16 text-center text-sm text-slate-500">No admission offers have been issued to you.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
