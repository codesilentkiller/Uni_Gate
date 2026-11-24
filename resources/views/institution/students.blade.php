<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Students — {{ $institution->name }}</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-8">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-sm text-slate-500">Accepted applicant register</p>
                <h1 class="mt-1 text-2xl font-bold">Student records</h1>
            </div>
            <span class="rounded-full bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700">{{ $students->count() }} accepted</span>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Student</th>
                            <th class="px-6 py-4">Student ID</th>
                            <th class="px-6 py-4">Programme</th>
                            <th class="px-6 py-4">Session</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($students as $student)
                            <tr>
                                <td class="px-6 py-5">
                                    <p class="font-semibold">{{ $student->applicant->user->first_name }} {{ $student->applicant->user->last_name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $student->application_reference }}</p>
                                </td>
                                <td class="px-6 py-5 font-mono text-xs font-bold">{{ $student->student_id }}</td>
                                <td class="px-6 py-5">{{ $student->programme->name }}</td>
                                <td class="px-6 py-5">{{ $student->admissionCycle->academicSession->name }}</td>
                                <td class="px-6 py-5">
                                    @if($student->enrolled_at)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Enrolled</span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Accepted</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    @if(! $student->enrolled_at)
                                        <form method="POST" action="{{ route('institution.applications.enroll', [$institution, $student]) }}" class="flex min-w-[280px] flex-col gap-2">
                                            @csrf
                                            <input name="course_level" required value="{{ old('course_level', $student->course_level ?: '100 Level') }}" placeholder="Course level" class="rounded-lg border-slate-300 text-xs">
                                            <select name="entry_status" required class="rounded-lg border-slate-300 text-xs">
                                                @foreach(['regular'=>'Regular','transfer'=>'Transfer','postgraduate'=>'Postgraduate','part_time'=>'Part-time','distance'=>'Distance'] as $value => $label)
                                                    <option value="{{ $value }}" @selected(old('entry_status', $student->entry_status ?: 'regular') === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <label class="flex items-center gap-2 text-xs text-slate-600">
                                                <input type="checkbox" name="orientation_completed" value="1" class="rounded border-slate-300">
                                                Orientation completed
                                            </label>
                                            <button class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white">Enroll student</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-500">{{ $student->enrolled_at->format('d M Y') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500">No accepted applicants are available for enrollment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
