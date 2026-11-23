<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institution;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['roles', 'institutions'])
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('platform.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $institutions = Institution::orderBy('name')->get();
        $roles = Role::orderBy('scope')->orderBy('name')->get();

        return view('platform.users.create', compact('institutions', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'institution_id' => ['nullable', 'exists:institutions,id'],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if ($role->scope === 'institution' && empty($validated['institution_id'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'institution_id' => 'An institution is required for institution-scoped roles.',
            ]);
        }

        if ($role->scope !== 'institution') {
            $validated['institution_id'] = null;
        }

        DB::transaction(function () use ($validated, $role, $request) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);

            if (! empty($validated['institution_id'])) {
                $institution = Institution::findOrFail($validated['institution_id']);
                $institution->users()->attach($user->id, ['status' => 'active', 'joined_at' => now()]);
            }

            $user->roles()->attach($role->id, [
                'institution_id' => $validated['institution_id'],
                'assigned_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('platform.users.index')->with('status', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
