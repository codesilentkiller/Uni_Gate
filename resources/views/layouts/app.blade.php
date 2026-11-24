<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UniGate') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-950">
@php
    $user = auth()->user();
    $roles = $user?->roles()->get() ?? collect();
    $roleSlugs = $roles->pluck('slug')->all();
    $institution = request()->route('institution');
    if (! $institution instanceof \App\Models\Institution) {
        $institution = $user?->institutions()->wherePivot('status', 'active')->first();
    }
    $isPlatform = (bool) array_intersect($roleSlugs, ['super-administrator','platform-support-administrator','platform-auditor']);
    $isInstitution = (bool) array_intersect($roleSlugs, ['university-administrator','admissions-officer','registrar','finance-officer','department-faculty-officer','document-verification-officer','read-only-staff']);
@endphp
<div class="min-h-screen">
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-[#071b3c] px-5 py-6 text-white lg:flex">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-500 text-sm font-black">U</span>
            <div><p class="font-bold">UniGate</p><p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-blue-200">{{ $isPlatform ? 'Platform' : ($isInstitution ? 'Institution' : 'Applicant') }}</p></div>
        </a>

        <nav class="mt-9 space-y-1 text-sm">
            @if($isPlatform)
                <a href="{{ route('platform.dashboard') }}" class="block rounded-lg px-4 py-3 {{ request()->routeIs('platform.dashboard') ? 'bg-white/10' : 'text-slate-300 hover:bg-white/10' }}">Overview</a>
                <a href="{{ route('platform.institutions.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Universities</a>
                <a href="{{ route('platform.institutions.create') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Onboard university</a>
                <a href="{{ route('platform.users.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Platform users</a>
                <a href="{{ route('platform.roles.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Roles &amp; permissions</a>
                <a href="{{ route('platform.reports') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Reports</a>
                <a href="{{ route('platform.audit-logs') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Audit logs</a>
                <a href="{{ route('platform.settings') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Settings</a>
            @elseif($isInstitution && $institution)
                <a href="{{ route('institution.dashboard', $institution) }}" class="block rounded-lg px-4 py-3 {{ request()->routeIs('institution.dashboard') ? 'bg-white/10' : 'text-slate-300 hover:bg-white/10' }}">Overview</a>
                <a href="{{ route('institution.profile.edit', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Institution profile</a>
                <a href="{{ route('institution.faculties', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Faculties</a>
                <a href="{{ route('institution.departments', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Departments</a>
                <a href="{{ route('institution.programmes', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Programmes</a>
                <a href="{{ route('institution.cycles', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Admission cycles</a>
                <a href="{{ route('institution.applications', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Applications</a>
                <a href="{{ route('institution.applications', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Admissions</a>
                <a href="{{ route('institution.admission-offers.index', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Admission offers</a>
                <a href="{{ route('institution.reports', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Reports</a>
                <a href="{{ route('institution.students', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Students</a>
                <a href="{{ route('institution.finance', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Finance</a>
                <a href="{{ route('institution.staff', $institution) }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Staff</a>
            @else
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-white/10' : 'text-slate-300 hover:bg-white/10' }}">Overview</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">My profile</a>
                <a href="{{ route('universities.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Explore universities</a>
                <a href="{{ route('applicant.applications.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">My applications</a>
                <a href="{{ route('applicant.documents.index') }}" class="block rounded-lg px-4 py-3 {{ request()->routeIs('applicant.documents.*') ? 'bg-white/10' : 'text-slate-300 hover:bg-white/10' }}">Documents</a>
                <a href="{{ route('applicant.admission-offers.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Admission offers</a>
                <a href="{{ route('applicant.payments') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Payments</a>
                <a href="{{ route('applicant.notifications.index') }}" class="flex items-center justify-between rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10"><span>Notifications</span>@if(auth()->user()->unreadNotifications()->count())<span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">{{ auth()->user()->unreadNotifications()->count() }}</span>@endif</a>
            @endif
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="mt-auto">
            @csrf
            <button class="w-full rounded-lg border border-white/10 px-4 py-3 text-left text-sm font-semibold text-slate-300 hover:bg-white/10">Sign out</button>
        </form>
    </aside>

    <div class="lg:ml-64">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="history.length > 1 ? history.back() : window.location.href='{{ route('dashboard') }}'" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        <span aria-hidden="true">←</span> Back
                    </button>
                    @isset($header)<div class="hidden border-l border-slate-200 pl-4 sm:block"><div class="text-sm font-bold text-slate-800">{{ $header }}</div></div>@endisset
                </div>
                <div class="flex items-center gap-3"><div class="hidden text-right sm:block"><p class="text-sm font-bold text-slate-800">{{ $user?->name }}</p><p class="text-xs text-slate-500">{{ $user?->email }}</p></div><a href="{{ route('profile.edit') }}" class="grid h-9 w-9 place-items-center rounded-full bg-blue-700 text-xs font-black text-white">{{ strtoupper(substr($user?->first_name ?? 'U',0,1)) }}</a></div>
            </div>
        </header>
        <main>{{ $slot }}</main>
    </div>
</div>
</body>
</html>
