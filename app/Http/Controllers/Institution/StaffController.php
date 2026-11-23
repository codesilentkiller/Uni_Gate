<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Institution $institution): View
    {
        $staff = $institution->users()
            ->with(['roles' => fn ($query) => $query->wherePivot('institution_id', $institution->id)])
            ->get();

        $roles = Role::where('scope','institution')->orderBy('name')->get();

        return view('institution.staff', compact('institution','staff','roles'));
    }

    public function store(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'phone' => ['nullable','string','max:30'],
            'role_id' => ['required','exists:roles,id'],
            'password' => ['required','string','min:8'],
        ]);

        $role = Role::whereKey($validated['role_id'])->where('scope','institution')->firstOrFail();

        DB::transaction(function () use ($institution,$validated,$role,$request) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);

            $institution->users()->attach($user->id, [
                'status' => 'active',
                'joined_at' => now(),
            ]);

            $user->roles()->attach($role->id, [
                'institution_id' => $institution->id,
                'assigned_by' => $request->user()->id,
            ]);
        });

        return back()->with('status','Institution staff account created and assigned successfully.');
    }
}
