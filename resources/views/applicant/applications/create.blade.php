<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">New Application</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl p-8">
        <form method="POST" action="{{ route('applicant.applications.store') }}" enctype="multipart/form-data" class="rounded-2xl border bg-white p-8">
            @csrf
            <input type="hidden" name="institution_id" value="{{ $institution->id }}">
            <input type="hidden" name="admission_cycle_id" value="{{ $cycle->id }}">

            <div>
                <p class="text-sm font-bold text-blue-700">{{ $institution->institution_code }} · {{ $cycle->name }}</p>
                <h1 class="mt-2 text-3xl font-bold">Create application</h1>
                <p class="mt-2 text-slate-500">Select a programme available in this admission cycle.</p>
            </div>

            @if($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="mt-8">
                <label class="text-sm font-semibold">Programme</label>
                <select name="programme_id" required class="mt-2 w-full rounded-lg border-slate-300">
                    @foreach($programmes as $programme)
                        <option value="{{ $programme->id }}">{{ $programme->name }} — {{ $programme->award_type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6">
                <label class="text-sm font-semibold">Entry level</label>
                <input name="entry_level" value="{{ old('entry_level','100 Level') }}" class="mt-2 w-full rounded-lg border-slate-300">
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold">Date of birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Gender</label>
                    <select name="gender" class="mt-2 w-full rounded-lg border-slate-300">
                        <option value="">Prefer not to say</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold">Nationality</label>
                    <input name="nationality" value="{{ old('nationality','Sierra Leonean') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">National ID / Passport number</label>
                    <input name="national_id" value="{{ old('national_id') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">Country of residence</label>
                    <input name="country_of_residence" value="{{ old('country_of_residence','Sierra Leone') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold">WASSCE index number</label>
                    <input name="wassce_index_number" value="{{ old('wassce_index_number') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="text-sm font-semibold">WASSCE year</label>
                    <input type="number" name="wassce_year" min="1990" max="{{ now()->year }}" value="{{ old('wassce_year') }}" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm font-semibold">WASSCE subjects and grades</p>
                <p class="mt-1 text-xs text-slate-500">Enter at least six subjects, including English Language and Mathematics.</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    @for($i=0;$i<6;$i++)
                        <div class="grid grid-cols-[1fr_110px] gap-2">
                            <input name="subjects[{{ $i }}][subject]" value="{{ old('subjects.'.$i.'.subject') }}" placeholder="Subject" class="rounded-lg border-slate-300 text-sm">
                            <select name="subjects[{{ $i }}][grade]" class="rounded-lg border-slate-300 text-sm">
                                <option value="">Grade</option>
                                @foreach(['A1','B2','B3','C4','C5','C6','D7','E8','F9'] as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold">Original WASSCE result / certificate copy</label>
                    <input type="file" name="wassce_certificate" accept=".pdf,.jpg,.jpeg,.png" required class="mt-2 block w-full text-sm">
                    <p class="mt-1 text-xs text-slate-500">PDF, JPG or PNG, maximum 5 MB.</p>
                </div>
                <div>
                    <label class="text-sm font-semibold">National ID card / Passport copy</label>
                    <input type="file" name="id_card" accept=".pdf,.jpg,.jpeg,.png" required class="mt-2 block w-full text-sm">
                    <p class="mt-1 text-xs text-slate-500">PDF, JPG or PNG, maximum 5 MB.</p>
                </div>
            </div>

            <div class="mt-6">
                <label class="text-sm font-semibold">Personal statement</label>
                <textarea name="personal_statement" rows="7" class="mt-2 w-full rounded-lg border-slate-300" placeholder="Describe your academic interests and motivation.">{{ old('personal_statement') }}</textarea>
            </div>

            <div class="mt-8 flex items-center justify-between">
                <a href="{{ route('universities.show',$institution) }}" class="text-sm font-semibold text-slate-600">Cancel</a>
                <button class="rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white">Create draft</button>
            </div>
        </form>
    </div>
</x-app-layout>
