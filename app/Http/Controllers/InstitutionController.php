<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $institutions = Institution::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('institutions.index', compact('institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Institution $institution): View
    {
        abort_unless($institution->status === 'active', 404);

        $openCycles = $institution->admissionCycles()
            ->where('status', 'open')
            ->where('opens_at', '<=', now())
            ->where('closes_at', '>=', now())
            ->with('programmes')
            ->get();

        $institution->load([
            'faculties.departments.programmes',
        ]);

        return view('institutions.show', compact('institution', 'openCycles'));
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
