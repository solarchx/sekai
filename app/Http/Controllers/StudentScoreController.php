<?php

namespace App\Http\Controllers;

use App\Models\StudentScore;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class StudentScoreController extends Controller
{
    public function index()
    {
        $scores = StudentScore::with('activity.subject', 'activity.teacher', 'activity.class', 'student')->get();
        return view('student-scores.index', compact('scores'));
    }

    public function create()
    {
        $activities = Activity::with('subject', 'teacher', 'class')->get();
        return view('student-scores.create', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'score' => 'required|integer|between:0,100',
        ]);

        // Check for duplicate
        $exists = StudentScore::where('activity_id', $validated['activity_id'])
            ->where('student_id', $validated['student_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A score with this name already exists for this student in this activity.']);
        }

        StudentScore::create($validated);

        return redirect()->route('student-scores.index')->with('success', 'Student score created successfully.');
    }

    public function edit(StudentScore $studentScore)
    {
        $activities = Activity::with('subject', 'teacher', 'class')->get();
        return view('student-scores.edit', compact('studentScore', 'activities'));
    }

    public function update(Request $request, StudentScore $studentScore)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'student_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'score' => 'required|integer|between:0,100',
        ]);

        $studentScore->update($validated);

        return redirect()->route('student-scores.index')->with('success', 'Student score updated successfully.');
    }

    public function destroy(StudentScore $studentScore)
    {
        $studentScore->delete();

        return redirect()->route('student-scores.index')->with('success', 'Student score deleted successfully.');
    }
}
