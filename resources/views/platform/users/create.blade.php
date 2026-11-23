<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Create platform user</h2></x-slot>
    <div class="mx-auto max-w-3xl p-8">
        <form method="POST" action="{{ route('platform.users.store') }}" class="rounded-2xl border bg-white p-8">
            @csrf
            <h1 class="text-2xl font-bold">Create user</h1>

            @if($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div><label class="text-sm font-semibold">First name</label><input name="first_name" value="{{ old('first_name') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Last name</label><input name="last_name" value="{{ old('last_name') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Password</label><input type="password" name="password" minlength="8" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Role</label><select name="role_id" required class="mt-2 w-full rounded-lg border-slate-300"><option value="">Select role</option>@foreach($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }} ({{ $role->scope }})</option>@endforeach</select></div>
                <div><label class="text-sm font-semibold">Institution</label><select name="institution_id" class="mt-2 w-full rounded-lg border-slate-300"><option value="">None — platform-wide</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}" @selected(old('institution_id') == $institution->id)>{{ $institution->name }}</option>@endforeach</select></div>
            </div>

            <div class="mt-8 flex justify-between"><a href="{{ route('platform.users.index') }}" class="text-sm font-semibold text-slate-600">Cancel</a><button class="rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white">Create user</button></div>
        </form>
    </div>
</x-app-layout>
