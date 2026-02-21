<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Subject;
use App\Models\User;
use App\Models\LessonPeriod;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('subject', 'teacher', 'period', 'class')->get();
        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $teachers = User::where('role', '!=', 'STUDENT')->get();
        $periods = LessonPeriod::with('semester')->get();
        $classes = SchoolClass::all();
        return view('activities.create', compact('subjects', 'teachers', 'periods', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'period_id' => 'required|exists:lesson_periods,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Check for teacher overlap
        $conflictingActivities = Activity::where('teacher_id', $validated['teacher_id'])
            ->whereHas('period', function ($query) use ($validated) {
                $period = LessonPeriod::find($validated['period_id']);
                $query->where('semester_id', $period->semester_id)
                    ->where('weekday', $period->weekday)
                    ->where(function ($q) use ($period) {
                        $q->whereRaw("TIME(time_end) > ?", [$period->time_begin])
                          ->whereRaw("TIME(time_begin) < ?", [$period->time_end]);
                    });
            })
            ->exists();

        if ($conflictingActivities) {
            return back()->withErrors(['teacher_id' => 'Teacher has overlapping activities.']);
        }

        // Check for duplicate activity
        $duplicate = Activity::where('subject_id', $validated['subject_id'])
            ->where('teacher_id', $validated['teacher_id'])
            ->where('period_id', $validated['period_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['subject_id' => 'This activity combination already exists.']);
        }

        $activity = Activity::create($validated);

        // Assign activity to all students in the class
        $students = SchoolClass::find($validated['class_id'])->students()->where('role', 'STUDENT')->pluck('id');
        foreach ($students as $index => $studentId) {
            $activity->students()->attach($studentId, ['student_order' => $index + 1]);
        }

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        $subjects = Subject::all();
        $teachers = User::where('role', '!=', 'STUDENT')->get();
        $periods = LessonPeriod::with('semester')->get();
        $classes = SchoolClass::all();
        return view('activities.edit', compact('activity', 'subjects', 'teachers', 'periods', 'classes'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'period_id' => 'required|exists:lesson_periods,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Check for teacher overlap (excluding current activity)
        $conflictingActivities = Activity::where('teacher_id', $validated['teacher_id'])
            ->where('id', '!=', $activity->id)
            ->whereHas('period', function ($query) use ($validated) {
                $period = LessonPeriod::find($validated['period_id']);
                $query->where('semester_id', $period->semester_id)
                    ->where('weekday', $period->weekday)
                    ->where(function ($q) use ($period) {
                        $q->whereRaw("TIME(time_end) > ?", [$period->time_begin])
                          ->whereRaw("TIME(time_begin) < ?", [$period->time_end]);
                    });
            })
            ->exists();

        if ($conflictingActivities) {
            return back()->withErrors(['teacher_id' => 'Teacher has overlapping activities.']);
        }

        $activity->update($validated);

        // If class changed, update students
        if ($activity->wasChanged('class_id')) {
            $activity->students()->detach();
            $students = SchoolClass::find($validated['class_id'])->students()->where('role', 'STUDENT')->pluck('id');
            foreach ($students as $index => $studentId) {
                $activity->students()->attach($studentId, ['student_order' => $index + 1]);
            }
        }

        return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully.');
    }
}
