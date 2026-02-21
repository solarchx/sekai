<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::all();
        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        return view('grades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|unique:grades,id',
        ]);

        Grade::create($validated);

        return redirect()->route('grades.index')->with('success', 'Grade created successfully.');
    }

    public function edit(Grade $grade)
    {
        return view('grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'id' => 'required|integer|unique:grades,id,' . $grade->id . ',id',
        ]);

        $grade->update($validated);

        return redirect()->route('grades.index')->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->route('grades.index')->with('success', 'Grade deleted successfully.');
    }
}
