<?php

namespace App\Http\Controllers;

use App\Models\LessonPeriod;
use App\Models\AcademicSemester;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonPeriodController extends Controller
{
    public function index(Request $request)
    {
        try {
            $semesters = AcademicSemester::all();
            $selectedSemesterId = $request->query('semester_id');
            $parentPeriods = collect();
            $periods = collect();
            $activities = collect();

            if ($selectedSemesterId) {
                // Parent periods (time slots)
                $parentPeriods = LessonPeriod::with('semester')
                    ->where('semester_id', $selectedSemesterId)
                    ->whereNull('parent_id')
                    ->get();

                // All child periods for this semester
                $periods = LessonPeriod::where('semester_id', $selectedSemesterId)->get();

                // Activities belonging to these child periods
                $activities = Activity::whereIn('period_id', $periods->pluck('id'))
                    ->with(['subject', 'teacher', 'class'])
                    ->get()
                    ->groupBy('period_id');
            }

            return view('periods.schedule-sheet', compact('semesters', 'selectedSemesterId', 'parentPeriods', 'periods', 'activities'));
        } catch (\Exception $e) {
            Log::error('Error loading periods: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading periods: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $semesters = AcademicSemester::all();
            if ($semesters->isEmpty()) {
                return redirect()->route('periods.index')->withErrors('No academic semesters available. Create one first.');
            }
            return view('periods.create', compact('semesters'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'time_begin' => 'required|date_format:H:i',
                'time_end' => 'required|date_format:H:i|after:time_begin',
                'semester_id' => 'required|exists:academic_semesters,id',
            ], [
                'time_begin.required' => 'Start time is required.',
                'time_begin.date_format' => 'Start time must be in HH:MM format.',
                'time_end.required' => 'End time is required.',
                'time_end.date_format' => 'End time must be in HH:MM format.',
                'time_end.after' => 'End time must be after start time.',
                'semester_id.required' => 'Academic semester is required.',
                'semester_id.exists' => 'Selected semester does not exist.',
            ]);

            // Start database transaction for ACID compliance
            DB::beginTransaction();

            // Create parent period that groups all 7 day periods
            $parentPeriod = LessonPeriod::create([
                'weekday' => 0, // Dummy value for parent
                'time_begin' => $validated['time_begin'],
                'time_end' => $validated['time_end'],
                'semester_id' => $validated['semester_id'],
                'parent_id' => null, // This is the parent
            ]);

            // Create 7 child periods (one for each day of the week: Monday 0 to Sunday 6)
            for ($day = 1; $day <= 6; $day++) {
            for ($day = 1; $day <= 6; $day++) {
                // Check for overlapping periods on this specific day
                $overlapping = LessonPeriod::where('weekday', $day)
                    ->where('semester_id', $validated['semester_id'])
                    ->whereNull('parent_id') // Only check against parent periods
                    ->where(function ($query) use ($validated) {
                        $query->whereRaw("TIME(time_end) > ?", [$validated['time_begin']])
                              ->whereRaw("TIME(time_begin) < ?", [$validated['time_end']]);
                    })
                    ->exists();

                if ($overlapping) {
                    DB::rollBack();
                    return back()->withErrors(['time_begin' => "This time overlaps with an existing period on day {$day}."]);
                }

                // Create child period for this day
                LessonPeriod::create([
                    'weekday' => $day,
                    'time_begin' => $validated['time_begin'],
                    'time_end' => $validated['time_end'],
                    'semester_id' => $validated['semester_id'],
                    'parent_id' => $parentPeriod->id,
                ]);
            }

            DB::commit();

            return redirect()->route('periods.index')->with('success', 'Lesson period created successfully for all 7 days.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error creating period: ' . $e->getMessage());
        }
    }

    public function edit(LessonPeriod $period)
    {
        try {
            // Only allow editing parent periods
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only edit parent periods directly.');
            }

            $semesters = AcademicSemester::all();
            return view('periods.edit', compact('period', 'semesters'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LessonPeriod $period)
    {
        try {
            // Only allow editing parent periods
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only edit parent periods directly.');
            }

            $validated = $request->validate([
                'time_begin' => 'required|date_format:H:i',
                'time_end' => 'required|date_format:H:i|after:time_begin',
                'semester_id' => 'required|exists:academic_semesters,id',
            ], [
                'time_begin.required' => 'Start time is required.',
                'time_begin.date_format' => 'Start time must be in HH:MM format.',
                'time_end.required' => 'End time is required.',
                'time_end.date_format' => 'End time must be in HH:MM format.',
                'time_end.after' => 'End time must be after start time.',
                'semester_id.required' => 'Academic semester is required.',
                'semester_id.exists' => 'Selected semester does not exist.',
            ]);

            DB::beginTransaction();

            // Update all child periods (7 days)
            $childPeriods = $period->childPeriods;

            foreach ($childPeriods as $child) {
                // Check for overlapping periods (excluding current child period)
                $overlapping = LessonPeriod::where('weekday', $child->weekday)
                    ->where('semester_id', $validated['semester_id'])
                    ->where('id', '!=', $child->id)
                    ->whereNull('parent_id')
                    ->where(function ($query) use ($validated) {
                        $query->whereRaw("TIME(time_end) > ?", [$validated['time_begin']])
                              ->whereRaw("TIME(time_begin) < ?", [$validated['time_end']]);
                    })
                    ->exists();

                if ($overlapping) {
                    DB::rollBack();
                    return back()->withErrors(['time_begin' => "This time overlaps with an existing period on day {$child->weekday}."]);
                }

                $child->update([
                    'time_begin' => $validated['time_begin'],
                    'time_end' => $validated['time_end'],
                    'semester_id' => $validated['semester_id'],
                ]);
            }

            // Update parent period
            $period->update([
                'time_begin' => $validated['time_begin'],
                'time_end' => $validated['time_end'],
                'semester_id' => $validated['semester_id'],
            ]);

            DB::commit();

            return redirect()->route('periods.index')->with('success', 'Lesson period updated successfully for all 7 days.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error updating period: ' . $e->getMessage());
        }
    }

    public function destroy(LessonPeriod $period)
    {
        try {
            // Only allow deleting parent periods
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only delete parent periods directly.');
            }

            DB::beginTransaction();

            // Delete all child periods first
            $period->childPeriods()->delete();

            // Delete parent period
            $period->delete();

            DB::commit();

            return redirect()->route('periods.index')->with('success', 'Lesson period deleted successfully (all 7 days removed).');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error deleting period: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted period (admin only).
     */
    public function restore(LessonPeriod $period)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }

        DB::transaction(function () use ($period) {
            $period->restore();
            $period->childPeriods()->restore();
        });

        return redirect()->route('periods.index')->with('success', 'Period restored.');
    }
}
