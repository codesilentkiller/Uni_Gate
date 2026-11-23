<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">My Applications</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        <a href="{{ route('dashboard') }}" class="mb-5 inline-flex text-sm font-semibold text-slate-600">← Back to applicant dashboard</a>

        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Track and manage university applications</p>
                <h1 class="mt-1 text-2xl font-bold">My Applications</h1>
            </div>
            <a href="{{ route('universities.index') }}" class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white">Explore universities</a>
        </div>

        @if(session('status'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="overflow-hidden rounded-2xl border bg-white">
            <table class="w-full text-sm">
                <thead class="border-b bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Reference</th>
                        <th>Institution</th>
                        <th>Programme</th>
                        <th>Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr class="border-b">
                            <td class="px-6 py-5 font-semibold">{{ $application->application_reference }}</td>
                            <td>{{ $application->institution->name }}</td>
                            <td>{{ $application->programme->name }}</td>
                            <td><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ str_replace('_',' ',ucwords($application->status)) }}</span></td>
                            <td class="px-6 py-5"><a href="{{ route('applicant.applications.show',$application) }}" class="font-semibold text-blue-700">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">You have not created any applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
