<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Platform Dashboard — UniGate</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100">
<div class="flex min-h-screen">
    <aside class="fixed inset-y-0 w-64 bg-[#071b3c] p-6 text-white">
        <b class="text-xl">UniGate Platform</b>
        <nav class="mt-10 space-y-2 text-sm">
            <a href="{{ route('platform.dashboard') }}" class="block rounded-lg bg-white/10 px-4 py-3">Overview</a>
            <a href="{{ route('platform.institutions.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Universities</a>
            <a href="{{ route('platform.institutions.create') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Institution Requests</a>
            <a href="{{ route('platform.users.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Platform Users</a>
            <a href="{{ route('platform.roles.index') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Roles &amp; Permissions</a>
            <a href="{{ route('platform.reports') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">System Reports</a>
            <a href="{{ route('platform.audit-logs') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Audit Logs</a>
            <a href="{{ route('platform.settings') }}" class="block rounded-lg px-4 py-3 text-slate-300 hover:bg-white/10">Settings</a>
        </nav>
    </aside>

    <main class="ml-64 flex-1">
        <header class="flex items-center justify-between border-b bg-white px-8 py-5">
            <div><p class="text-sm text-slate-500">Platform administration</p><h1 class="text-xl font-bold">System overview</h1></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg border px-4 py-2 text-sm font-semibold">Logout</button></form>
        </header>
        <div class="p-8">
            <div class="grid gap-5 md:grid-cols-4">
                @foreach([['Universities','2'],['Applicants','0'],['Applications','0'],['Admitted','0']] as $item)
                    <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">{{ $item[0] }}</p><p class="mt-3 text-3xl font-bold">{{ $item[1] }}</p></div>
                @endforeach
            </div>
            <section class="mt-8 rounded-2xl border bg-white p-8">
                <h2 class="text-xl font-bold">Platform activity</h2>
                <p class="mt-3 text-slate-500">No recent platform activity.</p>
            </section>
        </div>
    </main>
</div>
</body>
</html>
