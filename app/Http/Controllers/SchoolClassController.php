<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\Grade;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // old implementation, passing class id instead of user id
        //  $class->load('major', 'grade');
        //  return view('student.classes.show', compact('class'));
        $errorMessage1 = null;
        $errorMessage2 = null;
        $errorMessage3 = null;
        $class = null;
        try {
            $class = Auth::user()->class()->with(['major', 'grade'])->firstOrFail();
        } catch (Exception $e) {
            $errorMessage1 = "You are not assigned to any class.";
        }
        $homeroomTeacher = null;
        try {
            $homeroomTeacher = User::where('class_id', $class->id)->where('role', '!=', 'STUDENT')->firstOrFail();
        } catch (Exception $e) {
            $errorMessage2 = "You are not assigned to any class.";
        }
        $students = null;
        try {
            $students = User::where('class_id', $class->id)->where('role', 'STUDENT')->get();
        } catch (Exception $e) {
            $errorMessage3 = "There are no student in this class.";
        }
        $lessonTaught = Auth::user()->taughtActivities;
        return view('class.show', compact('class', 'lessonTaught', 'errorMessage1', 'errorMessage2', 'errorMessage3', 'homeroomTeacher', 'students'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClass $class)
    {
        $users = User::where('role', '!=', 'STUDENT')->get();
        $majors = Major::all();
        $grades = Grade::all();
        $homeroomTeacher = null;
        $errorMessage = null;
        try {
            $homeroomTeacher = User::where('class_id', $class->id)->where('role', '!=', 'STUDENT')->firstOrFail();
        } catch (Exception $e) {
            $errorMessage = "This class does not have a homeroom teacher.";
        }        
        return view('admin.classes.edit', compact('class', 'majors', 'grades', 'users', 'homeroomTeacher', 'errorMessage'));
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
        ]);

        $class->update($validated);

        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:users,id',
        ]);
        
        if (isset($validated['teacher_id'])) {
            User::where('class_id', $class->id)->where('role', '!=', 'STUDENT')->update(['class_id' => null]);
            User::where('id', $validated['teacher_id'])->update(['class_id' => $class->id]);
        }

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
