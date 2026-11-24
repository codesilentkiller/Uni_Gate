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
    <nav x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50 border-b border-slate-900/5 bg-white/95 text-slate-950 shadow-sm shadow-slate-950/5 backdrop-blur-xl">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between px-5 lg:px-8">
            <a href="#home" class="flex items-center gap-3" aria-label="UniGate home">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#071b3c] text-sm font-black text-white shadow-md shadow-blue-950/20">UG</span>
                <div>
                    <span class="block text-lg font-black leading-none tracking-tight">UniGate</span>
                    <span class="mt-1 block text-[9px] font-bold uppercase tracking-[0.2em] text-blue-700">Admission gateway</span>
                </div>
            </a>

            <div class="hidden items-center gap-7 lg:flex">
                <a class="nav-link text-blue-700" href="#home">Home</a>
                <a class="nav-link" href="#about">About</a>
                <a class="nav-link" href="{{ route('universities.index') }}">Universities</a>
                <a class="nav-link" href="#how">How it works</a>
                <a class="nav-link" href="#contact">Contact</a>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold transition hover:bg-slate-100">Log in</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-900/15 transition hover:-translate-y-0.5 hover:bg-blue-800">Create account</a>
            </div>

            <button @click="open = !open" type="button" class="grid h-11 w-11 place-items-center rounded-xl border border-slate-200 sm:hidden" aria-label="Toggle navigation" :aria-expanded="open">
                <svg x-show="!open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div x-show="open" x-cloak x-transition class="border-t border-slate-100 bg-white px-5 pb-5 sm:hidden">
            <div class="grid gap-1 pt-3 text-sm font-bold">
                <a @click="open=false" class="rounded-xl px-4 py-3 hover:bg-slate-50" href="#home">Home</a>
                <a @click="open=false" class="rounded-xl px-4 py-3 hover:bg-slate-50" href="#about">About</a>
                <a class="rounded-xl px-4 py-3 hover:bg-slate-50" href="{{ route('universities.index') }}">Universities</a>
                <a @click="open=false" class="rounded-xl px-4 py-3 hover:bg-slate-50" href="#how">How it works</a>
                <a @click="open=false" class="rounded-xl px-4 py-3 hover:bg-slate-50" href="#contact">Contact</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-bold">Log in</a>
                <a href="{{ route('register') }}" class="rounded-xl bg-blue-700 px-4 py-3 text-center text-sm font-bold text-white">Create account</a>
            </div>
        </div>
    </nav>

    <header id="home" class="relative isolate overflow-hidden bg-[#071b3c] pt-[72px] text-white">
        <div class="absolute -left-24 top-24 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute right-0 top-16 h-96 w-96 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 sm:py-20 lg:grid-cols-[1.02fr_.98fr] lg:px-8 lg:py-24">
            <div class="relative z-10">
                <div class="mb-7 inline-flex items-center gap-3 rounded-full border border-blue-300/20 bg-white/[0.07] px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-blue-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_14px_rgba(52,211,153,.9)]"></span>
                    Sierra Leone's admissions gateway
                </div>

                <h1 class="max-w-3xl text-5xl font-black leading-[1.02] tracking-tight sm:text-6xl lg:text-[68px]">
                    Your future begins with one
                    <span class="mt-2 block text-blue-400">clear application.</span>
                </h1>

                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300 sm:text-xl">
                    Discover universities, build one reusable profile, submit your applications and follow every decision from a single secure dashboard.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="group inline-flex items-center justify-center gap-3 rounded-xl bg-blue-500 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-blue-950/30 transition hover:-translate-y-1 hover:bg-blue-400">
                        Create free account
                        <span class="transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="{{ route('universities.index') }}" class="inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/[0.05] px-6 py-4 text-sm font-bold text-white transition hover:border-white/40 hover:bg-white/[0.1]">Explore universities</a>
                </div>

                <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm font-semibold text-slate-300">
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> One profile</span>
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Secure documents</span>
                    <span class="flex items-center gap-2"><span class="text-emerald-400">✓</span> Live status updates</span>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-xl lg:mr-0">
                <div class="absolute -inset-4 -rotate-2 rounded-[36px] border border-blue-300/20"></div>
                <div class="relative aspect-[4/3] overflow-hidden rounded-[28px] border border-white/15 bg-slate-900 shadow-2xl shadow-black/40">
                    <img src="{{ asset('images/hero-students.jpg') }}" alt="University students collaborating in a library" class="h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#071b3c]/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-5 left-5 right-5 flex items-end justify-between gap-4">
                        <div class="rounded-2xl border border-white/15 bg-[#071b3c]/90 p-4 backdrop-blur-md">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-blue-300">Applications</p>
                            <p class="mt-1 text-lg font-black">One journey, more choices</p>
                        </div>
                        <span class="hidden rounded-full bg-emerald-400 px-3 py-2 text-xs font-black text-emerald-950 sm:block">Ready to apply</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="problem" class="overflow-hidden bg-white">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-28">
            <div class="grid items-center gap-12 lg:grid-cols-[.94fr_1.06fr]">
                <div class="relative">
                    <div class="overflow-hidden rounded-[28px] bg-slate-100">
                        <img src="{{ asset('images/paperwork.jpg') }}" alt="Student preparing university application documents" class="h-[420px] w-full object-cover">
                    </div>
                    <div class="absolute -bottom-5 -right-3 w-52 rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/10 sm:right-[-18px]">
                        <p class="text-3xl font-black text-blue-700">One</p>
                        <p class="mt-1 text-sm font-bold text-slate-700">profile for every application</p>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">The challenge</p>
                    <h2 class="mt-4 text-4xl font-black leading-tight tracking-tight sm:text-5xl">Admissions should feel possible.</h2>
                    <p class="mt-6 text-lg leading-8 text-slate-600">Instead of uncertainty and repeated paperwork, UniGate gives applicants clear requirements, one reusable profile and a visible path from application to decision.</p>

                    <div class="mt-8 grid gap-3 sm:grid-cols-2">
                        @foreach([
                            ['01','Repeated processes','Enter information once and reuse it.'],
                            ['02','Limited visibility','Know what is complete and what comes next.'],
                            ['03','Document confusion','Keep documents organized and secure.'],
                            ['04','Disconnected decisions','Follow every offer in one place.']
                        ] as $item)
                            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5"><span class="text-xs font-black text-blue-600">{{ $item[0] }}</span><h3 class="mt-3 font-bold">{{ $item[1] }}</h3><p class="mt-1 text-sm leading-6 text-slate-600">{{ $item[2] }}</p></article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="evidence" class="relative overflow-hidden bg-[#0b2a57] text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_30%,rgba(59,130,246,.28),transparent_32%),radial-gradient(circle_at_85%_70%,rgba(45,212,191,.12),transparent_28%)]"></div>
        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-5 py-20 lg:grid-cols-[1.05fr_.95fr] lg:px-8 lg:py-24">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-cyan-300">Built around real students</p>
                <h2 class="mt-4 text-4xl font-black leading-tight sm:text-5xl">400 voices shaped a clearer journey.</h2>
                <p class="mt-6 max-w-xl text-lg leading-8 text-blue-100">Our survey of Sierra Leonean applicants highlighted fragmented processes, unclear requirements and the need for reliable status updates.</p>
                <div class="mt-9 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 backdrop-blur"><p class="text-4xl font-black">400</p><p class="mt-2 text-sm font-bold text-cyan-200">students surveyed</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 backdrop-blur"><p class="text-4xl font-black">1 goal</p><p class="mt-2 text-sm font-bold text-cyan-200">make access transparent</p></div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-3 rotate-2 rounded-[32px] bg-cyan-300/10"></div>
                <img src="{{ asset('images/study.jpg') }}" alt="Students working together in a digital campus environment" class="relative h-[420px] w-full rounded-[28px] object-cover shadow-2xl">
                <div class="absolute bottom-5 left-5 max-w-[260px] rounded-2xl bg-white p-5 text-slate-950 shadow-xl">
                    <p class="text-xs font-black uppercase tracking-wider text-blue-700">Research conclusion</p>
                    <p class="mt-2 font-bold leading-6">Applicants need one reusable, visible and secure admissions process.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="solution" class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8 lg:py-28">
            <div class="grid items-end gap-8 md:grid-cols-2">
                <div><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">The UniGate solution</p><h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">One account. Many possibilities.</h2></div>
                <p class="max-w-xl text-lg leading-8 text-slate-600 md:justify-self-end">A shared applicant experience stays simple, while universities retain full control of their programmes, decisions and institutional data.</p>
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach([
                    ['For applicants','Build once','Create a profile and apply to multiple universities without repeating every detail.','01'],
                    ['For universities','Manage independently','Control programmes, admissions cycles, applications and offers in a dedicated workspace.','02'],
                    ['For the gateway','Stay connected','Monitor participation and application activity with secure, authorized oversight.','03']
                ] as $card)
                    <article class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 shadow-sm"><span class="absolute right-6 top-5 text-5xl font-black text-slate-100">{{ $card[3] }}</span><p class="relative text-xs font-black uppercase tracking-widest text-blue-700">{{ $card[0] }}</p><h3 class="relative mt-5 text-2xl font-black">{{ $card[1] }}</h3><p class="relative mt-4 leading-7 text-slate-600">{{ $card[2] }}</p></article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="how" class="bg-white">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 py-20 lg:grid-cols-[.88fr_1.12fr] lg:px-8 lg:py-24">
            <div class="relative overflow-hidden rounded-[28px]">
                <img src="{{ asset('images/phone.jpg') }}" alt="A mobile phone displaying a connected digital platform" class="h-[500px] w-full object-cover">
                <div class="absolute inset-x-5 bottom-5 rounded-2xl border border-white/20 bg-[#071b3c]/90 p-5 text-white backdrop-blur-md">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-300">Application status</p>
                    <div class="mt-3 flex items-center justify-between"><p class="font-bold">Documents verified</p><span class="rounded-full bg-emerald-400 px-3 py-1 text-xs font-black text-emerald-950">Complete</span></div>
                </div>
            </div>

            <div>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">How it works</p>
                <h2 class="mt-4 text-4xl font-black leading-tight tracking-tight sm:text-5xl">From profile to admission.</h2>
                <div class="mt-8 space-y-3">
                    @foreach([
                        ['1','Build your profile','Create an account and complete personal and academic information.'],
                        ['2','Choose a university','Explore institutions, programmes, fees and admission requirements.'],
                        ['3','Submit securely','Upload documents and submit an application with a unique reference.'],
                        ['4','Follow every outcome','Track review progress and respond to offers from your dashboard.']
                    ] as $step)
                        <article class="flex gap-5 rounded-2xl border border-slate-200 p-5 transition hover:border-blue-200 hover:bg-blue-50/40"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-blue-700 text-sm font-black text-white">{{ $step[0] }}</span><div><h3 class="font-bold">{{ $step[1] }}</h3><p class="mt-1 text-sm leading-6 text-slate-600">{{ $step[2] }}</p></div></article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="universities" class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
            <div class="relative isolate overflow-hidden rounded-[32px] bg-[#071b3c] text-white">
                <img src="{{ asset('images/campus.jpg') }}" alt="Graduates celebrating outside a university campus" class="absolute inset-0 h-full w-full object-cover opacity-35">
                <div class="absolute inset-0 bg-gradient-to-r from-[#071b3c] via-[#071b3c]/90 to-[#071b3c]/20"></div>
                <div class="relative flex flex-col items-start justify-between gap-8 px-7 py-12 sm:px-10 lg:flex-row lg:items-end lg:px-12 lg:py-14">
                    <div><p class="text-xs font-black uppercase tracking-[0.22em] text-blue-300">Find your next chapter</p><h2 class="mt-3 max-w-3xl text-3xl font-black sm:text-4xl">Explore universities and discover a programme that fits your ambition.</h2></div>
                    <a href="{{ route('universities.index') }}" class="shrink-0 rounded-xl bg-blue-500 px-6 py-3.5 text-center text-sm font-bold shadow-lg transition hover:-translate-y-1 hover:bg-blue-400">Explore universities →</a>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="bg-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 lg:grid-cols-[1.15fr_.85fr] lg:px-8 lg:py-28">
            <div><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-700">About UniGate</p><h2 class="mt-4 max-w-3xl text-4xl font-black leading-tight sm:text-5xl">A practical response to a fragmented admissions system.</h2><div class="mt-7 max-w-3xl space-y-5 text-lg leading-8 text-slate-600"><p>UniGate connects applicants, universities and institutional administrators through one secure, configurable admissions platform.</p><p>It provides shared access for discovery and application while each institution retains control over its programmes, cycles, staff and decisions.</p></div></div>
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8"><p class="text-sm font-black uppercase tracking-widest text-slate-400">Our goal</p><blockquote class="mt-5 text-2xl font-black leading-snug">Make every applicant feel informed.<br><span class="text-blue-700">Make every institution feel in control.</span></blockquote><div class="mt-8 space-y-4 border-t border-slate-200 pt-6"><p class="flex gap-3 text-sm text-slate-600"><span class="font-black text-blue-700">01</span> Centralized access for applicants</p><p class="flex gap-3 text-sm text-slate-600"><span class="font-black text-blue-700">02</span> Independent control for universities</p><p class="flex gap-3 text-sm text-slate-600"><span class="font-black text-blue-700">03</span> Transparent and auditable decisions</p></div></div>
        </div>
    </section>

    <section class="bg-blue-700 text-white">
        <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-14 lg:flex-row lg:items-center lg:px-8"><div><p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200">Your next step starts here</p><h2 class="mt-3 text-3xl font-black sm:text-4xl">One profile. One gateway. More possibilities.</h2></div><div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row"><a href="{{ route('register') }}" class="rounded-xl bg-white px-6 py-3.5 text-center text-sm font-bold text-blue-800 shadow-lg transition hover:-translate-y-1">Create account</a><a href="{{ route('universities.index') }}" class="rounded-xl border border-blue-300 px-6 py-3.5 text-center text-sm font-bold text-white transition hover:bg-blue-600">Explore universities</a></div></div>
    </section>

    <footer id="contact" class="bg-[#05142b] text-slate-400">
        <div class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
            <div class="grid gap-10 border-b border-white/10 pb-10 sm:grid-cols-2 lg:grid-cols-4">
                <div><div class="flex items-center gap-3"><span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-sm font-black text-white">UG</span><div><p class="font-black text-white">UniGate</p><p class="text-xs">Higher education, connected.</p></div></div><p class="mt-5 max-w-xs text-sm leading-6">A modern admissions gateway for applicants and universities in Sierra Leone.</p></div>
                <div><p class="text-sm font-black text-white">Explore</p><div class="mt-4 grid gap-3 text-sm"><a class="hover:text-white" href="#home">Home</a><a class="hover:text-white" href="#about">About UniGate</a><a class="hover:text-white" href="{{ route('universities.index') }}">Universities</a></div></div>
                <div><p class="text-sm font-black text-white">Admissions</p><div class="mt-4 grid gap-3 text-sm"><a class="hover:text-white" href="{{ route('register') }}">Create account</a><a class="hover:text-white" href="{{ route('login') }}">Log in</a><a class="hover:text-white" href="#how">How it works</a></div></div>
                <div><p class="text-sm font-black text-white">Contact</p><div class="mt-4 grid gap-3 text-sm"><p>Sierra Leone</p><a class="hover:text-white" href="mailto:support@unigate.local">support@unigate.local</a></div></div>
            </div>
            <div class="flex flex-col gap-3 pt-7 text-xs sm:flex-row sm:items-center sm:justify-between"><p>© {{ date('Y') }} UniGate. All rights reserved.</p><p>Secure university admissions gateway</p></div>
        </div>
    </footer>
</body>
</html>