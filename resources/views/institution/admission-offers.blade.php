<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Admission Offers — {{ $institution->name }}</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        <a href="{{ route('institution.dashboard', $institution) }}" class="mb-5 inline-flex text-sm font-semibold text-slate-600">← Back to institution dashboard</a>

        <div class="mb-6">
            <p class="text-sm text-slate-500">Institution admission offers</p>
            <h1 class="text-2xl font-bold">Admission Offers</h1>
        </div>

        <div class="overflow-hidden rounded-2xl border bg-white">
            <table class="w-full text-sm">
                <thead class="border-b bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Offer reference</th>
                        <th>Applicant</th>
                        <th>Programme</th>
                        <th>Status</th>
                        <th class="px-6 py-4">Expires</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr class="border-b">
                            <td class="px-6 py-5 font-semibold">{{ $offer->offer_reference }}</td>
                            <td>{{ $offer->application->applicant->user->first_name }} {{ $offer->application->applicant->user->last_name }}</td>
                            <td>{{ $offer->application->programme->name }}</td>
                            <td><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ str_replace('_',' ',ucwords($offer->offer_status)) }}</span></td>
                            <td class="px-6 py-5">{{ $offer->expires_at?->format('d M Y') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">No admission offers have been issued.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $offers->links() }}
        </div>
    </div>
</x-app-layout>
