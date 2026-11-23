<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Staff — {{ $institution->name }}</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        @if(session('status'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <section class="rounded-2xl border bg-white p-8">
            <h1 class="text-2xl font-bold">Add institution staff</h1>
            <form method="POST" action="{{ route('institution.staff.store', $institution) }}" class="mt-6 grid gap-5 md:grid-cols-2">
                @csrf
                <div>
                    <label class="text-sm font-semibold">First name</label>
                    <input name="first_name" value="{{ old('first_name') }}" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Last name</label>
                    <input name="last_name" value="{{ old('last_name') }}" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Role</label>
                    <select name="role_id" required class="mt-2 w-full rounded-lg border-slate-300">
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold">Temporary password</label>
                    <input type="password" name="password" minlength="8" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div class="md:col-span-2">
                    <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white">Create staff account</button>
                </div>
            </form>
        </section>

        <section class="mt-6 overflow-hidden rounded-2xl border bg-white">
            <div class="border-b px-6 py-5"><h2 class="text-lg font-bold">Institution staff</h2></div>
            <table class="w-full text-sm">
                <thead class="border-b bg-slate-50 text-left"><tr><th class="px-6 py-4">Name</th><th>Email</th><th>Role</th></tr></thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr class="border-b"><td class="px-6 py-4 font-semibold">{{ $member->name }}</td><td>{{ $member->email }}</td><td>{{ $member->roles->pluck('name')->join(', ') ?: '—' }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-10 text-center text-slate-500">No staff accounts yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
</x-app-layout>
