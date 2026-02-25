<?php

namespace App\Http\Controllers;

use App\Models\AcademicSemester;
use Illuminate\Http\Request;

class AcademicSemesterController extends Controller
{
    public function index()
    {
        $semesters = AcademicSemester::paginate(100);
        return view('semesters.index', compact('semesters'));
    }

    public function create()
    {
        return view('semesters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:9',
            'semester' => 'required|integer|between:1,2',
        ]);

        // Check for duplicate
        $exists = AcademicSemester::where('academic_year', $validated['academic_year'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['semester' => 'This semester already exists.']);
        }

        AcademicSemester::create($validated);

        return redirect()->route('semesters.index')->with('success', 'Semester created successfully.');
    }

    public function edit(AcademicSemester $semester)
    {
        return view('semesters.edit', compact('semester'));
    }

    public function update(Request $request, AcademicSemester $semester)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:9',
            'semester' => 'required|integer|between:1,2',
        ]);

        $semester->update($validated);

        return redirect()->route('semesters.index')->with('success', 'Semester updated successfully.');
    }

    public function destroy(AcademicSemester $semester)
    {
        $semester->delete();

        return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');
    }
}
