<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceptance Letter — {{ $application->application_reference }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-4xl p-6 md:p-10">
        <div class="mb-5 flex justify-end print:hidden">
            <button onclick="window.print()" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white">Print acceptance letter</button>
        </div>

        <article class="min-h-[900px] border bg-white p-10 shadow-sm md:p-16">
            <header class="border-b-4 border-blue-700 pb-6">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-700">Official Admission Letter</p>
                <h1 class="mt-3 text-3xl font-bold">{{ $application->institution->name }}</h1>
                <p class="mt-2 text-sm text-slate-500">Student Registration Office</p>
            </header>

            <div class="mt-10 flex justify-between gap-6 text-sm">
                <div><p class="text-slate-500">Student name</p><p class="mt-1 font-bold">{{ $application->applicant->user->name }}</p></div>
                <div class="text-right"><p class="text-slate-500">Student ID</p><p class="mt-1 font-bold">{{ $application->student_id }}</p></div>
            </div>

            <section class="mt-10 leading-8">
                <p>Dear {{ $application->applicant->user->first_name }},</p>
                <p class="mt-5">We are pleased to confirm your acceptance to <strong>{{ $application->institution->name }}</strong> for the <strong>{{ $application->programme->name }}</strong> programme.</p>
                <p class="mt-5">Your application reference is <strong>{{ $application->application_reference }}</strong>. This letter confirms your place for the {{ $application->admissionCycle->name }} admission session, subject to verification of the documents and qualifications submitted.</p>
            </section>

            @php
                $tuitionFee = $application->programme->tuition_fee
                    ?: $application->programme->department->faculty->tuition_fee
                    ?: 0;
            @endphp

            <dl class="mt-10 grid gap-4 rounded-xl bg-slate-50 p-6 sm:grid-cols-2">
                <div><dt class="text-xs uppercase text-slate-500">Programme</dt><dd class="mt-1 font-bold">{{ $application->programme->name }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">Entry level</dt><dd class="mt-1 font-bold">{{ $application->entry_level ?: '100 Level' }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">Application ID</dt><dd class="mt-1 font-bold">{{ $application->application_reference }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">Acceptance date</dt><dd class="mt-1 font-bold">{{ $application->accepted_at?->format('d M Y') }}</dd></div>
                <div><dt class="text-xs uppercase text-slate-500">Programme tuition fee</dt><dd class="mt-1 font-bold">Le {{ number_format((float) $tuitionFee, 2) }}</dd></div>
            </dl>

            <section class="mt-10 rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm leading-7 text-slate-700">
                <h2 class="font-bold text-slate-900">Payment instructions and university terms</h2>
                <p class="mt-3">Complete required fees through the university's officially approved payment channels before the stated registration deadline. Fees may vary by programme and faculty.</p>
                <p class="mt-2">Registration remains subject to document verification, timely payment, academic conduct, and compliance with institutional regulations and applicable Sierra Leonean laws.</p>
            </section>

            <footer class="mt-16 border-t pt-8 text-sm text-slate-600">
                <p>Issued electronically through UniGate</p>
                <p class="mt-2">{{ $application->institution->name }} · {{ $application->institution->address ?: 'Sierra Leone' }}</p>
            </footer>
        </article>
    </main>
</body>
</html>
