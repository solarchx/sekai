<?php

namespace App\Http\Controllers;

use App\Models\LessonPeriod;
use App\Models\AcademicSemester;
use Illuminate\Http\Request;

class LessonPeriodController extends Controller
{
    public function index()
    {
        $periods = LessonPeriod::with('semester')->get();
        return view('periods.index', compact('periods'));
    }

    public function create()
    {
        $semesters = AcademicSemester::all();
        return view('periods.create', compact('semesters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'weekday' => 'required|integer|between:0,6',
            'time_begin' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_begin',
            'semester_id' => 'required|exists:academic_semesters,id',
        ]);

        // Check for overlapping periods
        $overlapping = LessonPeriod::where('weekday', $validated['weekday'])
            ->where('semester_id', $validated['semester_id'])
            ->where(function ($query) use ($validated) {
                $query->whereRaw("TIME(time_end) > ?", [$validated['time_begin']])
                      ->whereRaw("TIME(time_begin) < ?", [$validated['time_end']]);
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time_begin' => 'This period overlaps with an existing period.']);
        }

        LessonPeriod::create($validated);

        return redirect()->route('periods.index')->with('success', 'Period created successfully.');
    }

    public function edit(LessonPeriod $period)
    {
        $semesters = AcademicSemester::all();
        return view('periods.edit', compact('period', 'semesters'));
    }

    public function update(Request $request, LessonPeriod $period)
    {
        $validated = $request->validate([
            'weekday' => 'required|integer|between:0,6',
            'time_begin' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_begin',
            'semester_id' => 'required|exists:academic_semesters,id',
        ]);

        // Check for overlapping periods (excluding current period)
        $overlapping = LessonPeriod::where('weekday', $validated['weekday'])
            ->where('semester_id', $validated['semester_id'])
            ->where('id', '!=', $period->id)
            ->where(function ($query) use ($validated) {
                $query->whereRaw("TIME(time_end) > ?", [$validated['time_begin']])
                      ->whereRaw("TIME(time_begin) < ?", [$validated['time_end']]);
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time_begin' => 'This period overlaps with an existing period.']);
        }

        $period->update($validated);

        return redirect()->route('periods.index')->with('success', 'Period updated successfully.');
    }

    public function destroy(LessonPeriod $period)
    {
        $period->delete();

        return redirect()->route('periods.index')->with('success', 'Period deleted successfully.');
    }
}
