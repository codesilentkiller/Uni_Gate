<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Institution $institution): View
    {
        return view('institution.profile', compact('institution'));
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'institution_code' => ['required','string','max:20','unique:institutions,institution_code,'.$institution->id],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:5000'],
            'website' => ['nullable','url','max:255'],
        ]);

        $institution->update($validated);

        return back()->with('status','Institution profile updated.');
    }
}
