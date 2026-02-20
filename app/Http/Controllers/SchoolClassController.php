<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\Grade;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = SchoolClass::with('major', 'grade')->get();
        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $majors = Major::all();
        $grades = Grade::all();
        return view('admin.classes.create', compact('majors', 'grades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'major_id' => 'required|exists:majors,id',
            'grade_id' => 'required',
            'capacity' => 'required|integer|min:1|max:100',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
        public function show(SchoolClass $class)
    {
        $class->load('name', 'major', 'grade', 'teacher');
        return view('student.classes.show', compact('class'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClass $class)
    {
        $majors = Major::all();
        $grades = Grade::all();
        return view('admin.classes.edit', compact('class', 'majors', 'grades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'major_id' => 'required|exists:majors,id',
            'grade_id' => 'required|exists:grades,id',
            'capacity' => 'required|integer|min:1|max:100',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }
}
