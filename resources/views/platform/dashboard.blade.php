<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Platform Overview</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        <a href="{{ route('dashboard') }}" class="mb-5 inline-flex text-sm font-semibold text-slate-600">← Back to
            dashboard</a>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Unified gateway administration</p>
                <h1 class="mt-1 text-3xl font-bold">Platform Overview</h1>
            </div>
            <a href="{{ route('platform.institutions.create') }}"
                class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white">Onboard university</a>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([['Universities',$stats['institutions'],'institution'],['Active universities',$stats['activeInstitutions'],'active'],['Users',$stats['users'],'account'],['Applicants',$stats['applicants'],'profile'],['Applications',$stats['applications'],'application'],['Submitted',$stats['submittedApplications'],'queue']] as $metric)
            <article class="rounded-2xl border bg-white p-6">
                <p class="text-sm text-slate-500">{{ $metric[0] }}</p>
                <p class="mt-3 text-3xl font-bold">{{ $metric[1] }}</p>
            </article>
            @endforeach
        </div>
        <section class="mt-8 rounded-2xl border bg-white p-6">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-lg font-bold">Participating universities</h2>
                <a href="{{ route('platform.institutions.index') }}"
                    class="text-sm font-semibold text-blue-700">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($institutions as $institution)
                <a href="{{ route('platform.institutions.show',$institution) }}"
                    class="flex items-center justify-between rounded-xl border p-4 hover:border-blue-200">
                    <div>
                        <p class="font-semibold">{{ $institution->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $institution->institution_code }}</p>
                    </div>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold">{{ ucwords($institution->status) }}</span>
                </a>
                @empty
                <p class="py-8 text-center text-sm text-slate-500">No universities onboarded.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
