<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $institutions = Institution::query()
            ->when($request->string('status')->toString(), fn ($query,$status) => $query->where('status',$status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('platform.institutions.index', compact('institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('platform.institutions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'institution_code' => ['required','string','max:20','unique:institutions,institution_code'],
            'slug' => ['required','string','max:255','alpha_dash','unique:institutions,slug'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:5000'],
            'status' => ['required','in:pending,active,suspended,inactive'],
            'admin_first_name' => ['nullable','string','max:255'],
            'admin_last_name' => ['nullable','string','max:255'],
            'admin_email' => ['nullable','email','max:255','unique:users,email'],
            'admin_password' => ['nullable','string','min:8'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $adminData = collect(['admin_first_name','admin_last_name','admin_email','admin_password'])->all();
            $institutionData = collect($validated)->except($adminData)->all();

            $institution = Institution::create($institutionData);

            Storage::disk('unigate-private')->makeDirectory('institutions/'.$institution->slug);

            foreach ([
                ['Passport Photograph', 'passport-photograph'],
                ['WASSCE Certificate', 'wassce-certificate'],
                ['National Identification Document', 'national-id'],
                ['Birth Certificate', 'birth-certificate'],
                ['Academic Transcript', 'academic-transcript'],
            ] as [$name, $slug]) {
                \App\Models\DocumentType::firstOrCreate(
                    ['institution_id' => $institution->id, 'slug' => $slug],
                    [
                        'name' => $name,
                        'allowed_extensions' => 'pdf,jpg,jpeg,png',
                        'max_file_size_kb' => 5120,
                        'status' => 'active',
                    ],
                );
            }

            if (! empty($validated['admin_email'])) {
                $admin = \App\Models\User::create([
                    'first_name' => $validated['admin_first_name'] ?: 'University',
                    'last_name' => $validated['admin_last_name'] ?: 'Administrator',
                    'email' => $validated['admin_email'],
                    'email_verified_at' => now(),
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['admin_password'] ?: 'password'),
                    'status' => 'active',
                ]);

                $institution->users()->attach($admin->id, ['status'=>'active','joined_at'=>now()]);

                $role = \App\Models\Role::where('slug','university-administrator')->firstOrFail();
                $admin->roles()->attach($role->id, ['institution_id'=>$institution->id,'assigned_by'=>auth()->id()]);
            }
        });

        return redirect()->route('platform.institutions.index')->with('status','University onboarded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution): View
    {
        $institution->loadCount(['users','applications','programmes','admissionCycles']);
        return view('platform.institutions.show', compact('institution'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required','in:pending,active,suspended,inactive'],
        ]);

        $institution->update($validated);

        return back()->with('status','Institution status updated.');
    }
}
