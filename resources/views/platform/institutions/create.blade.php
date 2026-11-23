<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Onboard University</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl p-8">
        <form method="POST" action="{{ route('platform.institutions.store') }}" class="rounded-2xl border bg-white p-8">
            @csrf

            <div>
                <p class="text-sm font-bold text-blue-700">PLATFORM ADMINISTRATION</p>
                <h1 class="mt-2 text-2xl font-bold">Onboard a university</h1>
            </div>

            @if($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="text-sm font-semibold">University name</label>
                    <input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Institution code</label>
                    <input name="institution_code" value="{{ old('institution_code') }}" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">URL slug</label>
                    <input name="slug" value="{{ old('slug') }}" required class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Official email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Phone</label>
                    <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-semibold">Address</label>
                    <textarea name="address" rows="3" class="mt-2 w-full rounded-lg border-slate-300">{{ old('address') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-semibold">Initial status</label>
                    <select name="status" class="mt-2 w-full rounded-lg border-slate-300">
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 border-t pt-6">
                <h2 class="text-lg font-bold">University administrator</h2>
                <p class="mt-1 text-sm text-slate-500">Leave blank if you want to create the institution without an administrator account.</p>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold">First name</label>
                        <input name="admin_first_name" value="{{ old('admin_first_name') }}" class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Last name</label>
                        <input name="admin_last_name" value="{{ old('admin_last_name') }}" class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Administrator email</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" class="mt-2 w-full rounded-lg border-slate-300"></input>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Temporary password</label>
                        <input type="password" name="admin_password" minlength="8" class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('platform.institutions.index') }}" class="text-sm font-semibold text-slate-600">Cancel</a>
                <button class="rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white">Create university</button>
            </div>
        </form>
    </div>
</x-app-layout>
