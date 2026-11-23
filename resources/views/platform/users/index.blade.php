<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Platform Users</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        <div class="mb-6">
            <p class="text-sm text-slate-500">Central account directory</p>
            <h1 class="text-2xl font-bold">Platform Users</h1>
        </div>

        <a href="{{ route('platform.users.create') }}" class="mb-5 inline-block rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white">Create user</a>

        @if(session('status'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" class="mb-5">
            <input name="search" value="{{ request('search') }}" placeholder="Search name or email"
                class="w-full max-w-md rounded-lg border-slate-300">
        </form>

        <div class="overflow-hidden rounded-2xl border bg-white">
            <table class="w-full text-sm">
                <thead class="border-b bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Institutions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b">
                            <td class="px-6 py-5 font-semibold">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>{{ $user->institutions->pluck('institution_code')->join(', ') ?: 'Platform-wide' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
