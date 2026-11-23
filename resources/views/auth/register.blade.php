<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create account — UniGate</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100">
<div class="grid min-h-screen lg:grid-cols-[420px_1fr]">
    <aside class="hidden bg-[#071b3c] p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <a href="/" class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-500 font-bold">U</span><span class="text-xl font-bold">UniGate</span></a>
        <div><h1 class="text-4xl font-bold">Start your higher education journey.</h1><p class="mt-5 text-slate-300">Create one profile and apply to participating universities.</p></div>
        <p class="text-sm text-slate-400">Centralized admissions. Institutional independence.</p>
    </aside>
    <main class="flex items-center justify-center px-6 py-12">
        <form method="POST" action="{{ route('register') }}" class="w-full max-w-xl rounded-2xl bg-white p-8 shadow-sm">
            @csrf
            <h2 class="text-3xl font-bold">Create your UniGate account</h2>
            <p class="mt-2 text-sm text-slate-500">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-blue-700">Sign in</a></p>
            @if($errors->any())<div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <div class="mt-8 grid gap-5 md:grid-cols-2">
                <div><label class="text-sm font-semibold">First name</label><input name="first_name" value="{{ old('first_name') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Middle name <span class="font-normal text-slate-400">(optional)</span></label><input name="middle_name" value="{{ old('middle_name') }}" class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Last name</label><input name="last_name" value="{{ old('last_name') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Phone number</label><input name="phone" value="{{ old('phone') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div class="md:col-span-2"><label class="text-sm font-semibold">Email address</label><input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Password</label><input type="password" name="password" required class="mt-2 w-full rounded-lg border-slate-300"></div>
                <div><label class="text-sm font-semibold">Confirm password</label><input type="password" name="password_confirmation" required class="mt-2 w-full rounded-lg border-slate-300"></div>
            </div>
            <label class="mt-6 flex items-start gap-3 text-sm text-slate-600"><input type="checkbox" name="terms" required class="mt-1 rounded"><span>I agree to the platform terms and privacy policy.</span></label>
            <button class="mt-6 w-full rounded-lg bg-blue-700 py-3.5 font-semibold text-white">Create Account</button>
        </form>
    </main>
</div>
</body>
</html>
