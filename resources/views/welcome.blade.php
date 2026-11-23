<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UniGate connects Sierra Leonean applicants with universities through one secure, transparent admissions platform.">
    <title>UniGate — Your Gateway to Higher Education</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-950">
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-[#071b3c]/95 text-white backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="/" class="flex items-center gap-3" aria-label="UniGate home">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-500 text-lg font-black shadow-lg shadow-blue-950/30">U</span>
                <div>
                    <span class="block text-lg font-bold leading-none">UniGate</span>
                    <span class="mt-1 block text-[10px] font-semibold uppercase tracking-[0.22em] text-blue-200">Admissions gateway</span>
                </div>
            </a>

            <div class="hidden items-center gap-8 text-sm font-semibold lg:flex">
                <a class="text-blue-200 transition hover:text-white" href="#problem">The problem</a>
                <a class="transition hover:text-blue-200" href="#solution">Our solution</a>
                <a class="transition hover:text-blue-200" href="#evidence">Research</a>
                <a class="transition hover:text-blue-200" href="#universities">Universities</a>
                <a class="transition hover:text-blue-200" href="#about">About</a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="hidden rounded-xl px-4 py-2.5 text-sm font-bold transition hover:bg-white/10 sm:block">Sign in</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-blue-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-950/30 transition hover:-translate-y-0.5 hover:bg-blue-400 sm:px-5">Apply now</a>
            </div>
        </div>
    </nav>

    <header class="relative isolate overflow-hidden bg-[#071b3c] pt-28 text-white">
        <div class="absolute -left-24 top-24 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute right-0 top-16 h-96 w-96 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px); background-size:64px 64px;"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-5 pb-20 pt-16 lg:grid-cols-[1.08fr_.92fr] lg:px-8 lg:pb-24 lg:pt-20">
            <div>
                <div class="mb-7 inline-flex items-center gap-3 rounded-full border border-blue-300/20 bg-white/[0.06] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-blue-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_14px_rgba(52,211,153,.9)]"></span>
                    Built for Sierra Leone's higher education journey
                </div>

                <h1 class="max-w-3xl text-5xl font-black leading-[1.02] tracking-tight sm:text-6xl lg:text-[72px]">
                    Access should never be the hardest part of
                    <span class="mt-2 block text-blue-400">getting an education.</span>
                </h1>

                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300 sm:text-xl">
                    UniGate gives students one clear place to discover universities, prepare applications, upload documents, track decisions and respond to offers—without starting again at every institution.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="group inline-flex items-center justify-center gap-3 rounded-xl bg-blue-500 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-blue-950/30 transition hover:-translate-y-1 hover:bg-blue-400">
                        Create your UniGate account
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="#problem" class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/[0.04] px-6 py-4 text-sm font-bold text-white transition hover:border-white/30 hover:bg-white/[0.08]">Why UniGate matters</a>
                </div>

                <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-slate-300">
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> One reusable profile</span>
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Multiple applications</span>
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Clear application tracking</span>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-xl">
                <div class="absolute -inset-5 -rotate-3 rounded-[32px] border border-blue-300/10 bg-blue-400/[0.05]"></div>
                <div class="relative overflow-hidden rounded-[28px] border border-white/10 bg-white p-5 shadow-2xl shadow-black/30">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-blue-700 font-black text-white">U</span>
                            <div><p class="text-xs font-bold uppercase tracking-widest text-slate-400">Applicant portal</p><p class="mt-0.5 font-bold text-slate-900">My applications</p></div>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">Application open</span>
                    </div>

                    <div class="pt-5">
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold text-blue-700">2027 INTAKE</p><h3 class="mt-2 font-bold text-slate-950">Limkokwing University</h3><p class="mt-1 text-sm text-slate-500">BSc Software Engineering</p></div><span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">Submitted</span></div>
                            <div class="mt-6 grid grid-cols-4 gap-2">
                                @foreach(['Profile','Documents','Review','Offer'] as $index => $step)
                                    <div><div class="h-2 rounded-full {{ $index < 3 ? 'bg-blue-600' : 'bg-slate-200' }}"></div><p class="mt-2 text-[10px] font-bold {{ $index < 3 ? 'text-blue-700' : 'text-slate-400' }}">{{ $step }}</p></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-black text-slate-950">1</p><p class="mt-1 text-xs font-semibold text-slate-500">University</p></div>
                            <div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-black text-slate-950">5</p><p class="mt-1 text-xs font-semibold text-slate-500">Documents</p></div>
                            <div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-black text-slate-950">Live</p><p class="mt-1 text-xs font-semibold text-slate-500">Tracking</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="problem" class="overflow-hidden bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-28">
            <div class="grid items-start gap-12 lg:grid-cols-[.85fr_1.15fr]">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">The challenge</p>
                    <h2 class="mt-4 text-4xl font-black leading-tight tracking-tight sm:text-5xl">The problem was never just paperwork.</h2>
                    <p class="mt-6 text-lg leading-8 text-slate-600">It was uncertainty. Students travelled between institutions, repeated information, waited for feedback and struggled to know whether their applications were complete, received or successful.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach([
                        ['01','Repeated processes','Personal, academic and document information had to be entered again for every university.'],
                        ['02','Limited visibility','Applicants could not easily track progress or know what was missing.'],
                        ['03','Document confusion','Requirements differed, files were misplaced and replacements were difficult to coordinate.'],
                        ['04','Disconnected decisions','Fees, applications and admission responses often followed separate manual processes.']
                    ] as $item)
                        <article class="group rounded-2xl border border-slate-200 bg-slate-50 p-6 transition hover:-translate-y-1 hover:border-blue-200 hover:bg-blue-50/60"><span class="text-xs font-black text-blue-600">{{ $item[0] }}</span><h3 class="mt-4 text-lg font-bold">{{ $item[1] }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $item[2] }}</p></article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="evidence" class="relative overflow-hidden bg-[#0b2a57] text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_30%,rgba(59,130,246,.28),transparent_32%),radial-gradient(circle_at_85%_70%,rgba(45,212,191,.12),transparent_28%)]"></div>
        <div class="relative mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-24">
            <div class="grid items-center gap-12 lg:grid-cols-[.92fr_1.08fr]">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-300">The research behind UniGate</p>
                    <h2 class="mt-4 text-4xl font-black leading-tight sm:text-5xl">We asked 400 students about the real experience.</h2>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-blue-100">The survey was used to understand how applicants navigated university admission and registration in Sierra Leone. It reinforced one central finding: the process felt fragmented, difficult to track and unnecessarily stressful.</p>
                </div>

                <div class="relative">
                    <div class="absolute -inset-3 rotate-2 rounded-3xl bg-cyan-300/10"></div>
                    <div class="relative grid grid-cols-2 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.07] backdrop-blur">
                        <div class="col-span-2 border-b border-white/10 p-7 sm:p-9"><p class="text-6xl font-black text-white sm:text-7xl">400</p><p class="mt-3 text-lg font-bold text-cyan-200">student voices surveyed</p></div>
                        <div class="border-r border-white/10 p-7"><p class="text-3xl font-black text-white">Fragmented</p><p class="mt-2 text-sm leading-6 text-blue-100">Admissions information and procedures were not experienced as one smooth journey.</p></div>
                        <div class="p-7"><p class="text-3xl font-black text-white">Confusing</p><p class="mt-2 text-sm leading-6 text-blue-100">Applicants needed clearer requirements, status updates and next steps.</p></div>
                        <div class="col-span-2 border-t border-white/10 p-7"><p class="text-sm leading-7 text-blue-100"><strong class="text-white">Research conclusion:</strong> higher education access requires a transparent, reusable and digitally coordinated admissions process.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="solution" class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-28">
            <div class="mx-auto max-w-3xl text-center"><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">The UniGate solution</p><h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">One account.<br>Many universities. Complete visibility.</h2><p class="mt-6 text-lg leading-8 text-slate-600">UniGate separates the shared applicant experience from institution-owned admissions processes—so students benefit from central access while universities retain control.</p></div>

            <div class="mt-14 grid gap-5 md:grid-cols-3">
                @foreach([
                    ['For applicants','One reusable profile','Create an account once, explore institutions and manage every application through a personal dashboard.','01'],
                    ['For universities','Independent institutional workspaces','Manage programmes, admission cycles, staff, applications, decisions and registrations without exposing another institution’s data.','02'],
                    ['For the gateway','A connected, measurable ecosystem','Monitor participating institutions, application volumes and platform activity through an authorized administrative layer.','03']
                ] as $card)
                    <article class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 shadow-sm"><span class="absolute right-6 top-5 text-5xl font-black text-slate-100">{{ $card[3] }}</span><p class="relative text-xs font-black uppercase tracking-widest text-blue-700">{{ $card[0] }}</p><h3 class="relative mt-5 text-2xl font-black">{{ $card[1] }}</h3><p class="relative mt-4 leading-7 text-slate-600">{{ $card[2] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="how" class="bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-24">
            <div class="grid items-end gap-8 md:grid-cols-2"><div><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">How it works</p><h2 class="mt-4 text-4xl font-black tracking-tight">From application to admission, all in one journey.</h2></div><p class="max-w-xl text-lg leading-8 text-slate-600 md:justify-self-end">A simple, transparent workflow replaces disconnected visits, repeated forms and delayed status updates.</p></div>

            <div class="mt-12 grid gap-4 md:grid-cols-4">
                @foreach([
                    ['1','Build your profile','Create an account and complete the personal and academic information used across applications.'],
                    ['2','Choose a university','Explore active institutions, programmes, requirements, fees and admission cycles.'],
                    ['3','Submit securely','Upload required documents, review the application and submit with a unique reference.'],
                    ['4','Follow every outcome','Track reviews, decisions and offers—and accept or decline without losing progress.']
                ] as $step)
                    <article class="rounded-2xl border border-slate-200 p-6"><span class="grid h-11 w-11 place-items-center rounded-xl bg-blue-700 text-sm font-black text-white">{{ $step[0] }}</span><h3 class="mt-6 font-bold">{{ $step[1] }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $step[2] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="universities" class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
            <div class="flex flex-col gap-6 rounded-3xl bg-[#071b3c] px-7 py-10 text-white sm:px-10 lg:flex-row lg:items-center lg:justify-between">
                <div><p class="text-xs font-black uppercase tracking-[0.22em] text-blue-300">Pilot institution</p><h2 class="mt-3 text-3xl font-black">Limkokwing University of Creative Technology, Sierra Leone</h2><p class="mt-3 max-w-2xl text-slate-300">Explore the pilot institution, its admissions process and available application journey.</p></div>
                <a href="{{ route('universities.index') }}" class="shrink-0 rounded-xl bg-blue-500 px-6 py-3.5 text-center text-sm font-bold shadow-lg transition hover:-translate-y-1 hover:bg-blue-400">Explore universities →</a>
            </div>
        </div>
    </section>

    <section id="about" class="bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-28">
            <div class="grid gap-12 lg:grid-cols-[1.15fr_.85fr]">
                <div><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">About UniGate</p><h2 class="mt-4 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">A practical response to a fragmented admissions system.</h2><div class="mt-7 max-w-3xl space-y-5 text-lg leading-8 text-slate-600"><p>UniGate is an academic project and configurable admissions gateway designed to connect applicants, universities and institutional administrators through one secure platform.</p><p>It is not intended to replace university management systems. Instead, it provides the shared admission layer: discovering institutions, submitting applications, handling documents, reviewing applicants and issuing offers. Each institution remains responsible for its own programmes, cycles and decisions.</p></div></div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8"><p class="text-sm font-black uppercase tracking-widest text-slate-400">Our goal</p><blockquote class="mt-5 text-2xl font-black leading-snug">Make every applicant feel informed.<br><span class="text-blue-700">Make every institution feel in control.</span></blockquote><div class="mt-8 space-y-4 border-t border-slate-200 pt-6"><p class="flex gap-3 text-sm text-slate-600"><span class="font-black text-blue-700">01</span> Centralized access for applicants</p><p class="flex gap-3 text-sm text-slate-600"><span class="font-black text-blue-700">02</span> Independent control for universities</p><p class="flex gap-3 text-sm text-slate-600"><span class="font-black text-blue-700">03</span> Transparent and auditable decisions</p></div></div>
            </div>
        </div>
    </section>

    <section class="bg-blue-700 text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-14 lg:flex-row lg:items-center lg:px-8"><div><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200">Your next step starts here</p><h2 class="mt-3 text-3xl font-black sm:text-4xl">One profile. One gateway. More possibilities.</h2></div><div class="flex flex-col gap-3 sm:flex-row"><a href="{{ route('register') }}" class="rounded-xl bg-white px-6 py-3.5 text-center text-sm font-bold text-blue-800 shadow-lg transition hover:-translate-y-1">Create your account</a><a href="{{ route('universities.index') }}" class="rounded-xl border border-blue-300 px-6 py-3.5 text-center text-sm font-bold text-white transition hover:bg-blue-600">Explore universities</a></div></div>
    </section>

    <footer class="bg-[#05142b] text-slate-400"><div class="mx-auto max-w-7xl px-5 py-10 lg:px-8"><div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between"><div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-xl bg-blue-600 text-sm font-black text-white">U</span><div><p class="font-bold text-white">UniGate</p><p class="text-xs">Higher education, connected.</p></div></div><p class="text-xs">© {{ date('Y') }} UniGate. Configurable admissions gateway for higher education institutions.</p></div></div></footer>
</body>
</html>
