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
                // Parent periods sorted by time_begin
                $parentPeriods = LessonPeriod::with('semester')
                    ->where('semester_id', $selectedSemesterId)
                    ->whereNull('parent_id')
                    ->orderBy('time_begin')               // <-- added
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
                'time_begin'  => 'required|date_format:H:i',
                'time_end'    => 'required|date_format:H:i|after:time_begin',
                'semester_id' => 'required|exists:academic_semesters,id',
            ]);

            DB::beginTransaction();

            $parentPeriod = null;

            // Create child periods for each day (1–6)
            for ($day = 0; $day <= 6; $day++) {
                // Check for overlapping periods on this specific day
                $overlapping = LessonPeriod::where('weekday', $day)
                    ->where('semester_id', $validated['semester_id'])
                    ->whereNull('parent_id')   // only parent periods represent time slots
                    ->where(function ($query) use ($validated) {
                        $query->where('time_end', '>', $validated['time_begin'])
                            ->where('time_begin', '<', $validated['time_end']);
                    })
                    ->exists();

                if ($overlapping) {
                    DB::rollBack();
                    return back()->withErrors([
                        'time_begin' => "The time range {$validated['time_begin']}–{$validated['time_end']} overlaps with an existing period on " . LessonPeriod::WEEKDAYS[$day] . "."
                    ])->withInput();
                }

                // Create child period for this day
                $currentPeriod = LessonPeriod::create([
                    'weekday'     => $day,
                    'time_begin'  => $validated['time_begin'],
                    'time_end'    => $validated['time_end'],
                    'semester_id' => $validated['semester_id'],
                    'parent_id'   => $parentPeriod ? $parentPeriod->id : null,
                ]);

                if ($day == 0) {
                    $parentPeriod = $currentPeriod;
                }
            }

            DB::commit();
            return redirect()->route('periods.index', ['semester_id' => $validated['semester_id']])
                ->with('success', 'Lesson period created successfully for all 7 days.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating period: ' . $e->getMessage());
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
            // Only parent periods can be edited directly
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only edit parent periods directly.');
            }

            $validated = $request->validate([
                'time_begin'  => 'required|date_format:H:i',
                'time_end'    => 'required|date_format:H:i|after:time_begin',
                'semester_id' => 'required|exists:academic_semesters,id',
            ]);

            DB::beginTransaction();

            // Check for overlapping parent periods in the same semester, excluding this one
            $overlapping = LessonPeriod::where('semester_id', $validated['semester_id'])
                ->whereNull('parent_id')
                ->where('id', '!=', $period->id)
                ->where(function ($query) use ($validated) {
                    $query->where('time_end', '>', $validated['time_begin'])
                        ->where('time_begin', '<', $validated['time_end']);
                })
                ->exists();

            if ($overlapping) {
                DB::rollBack();
                return back()->withErrors([
                    'time_begin' => "The updated time range overlaps with an existing period."
                ])->withInput();
            }

            // Update all child periods (one per weekday)
            $period->childPeriods()->update([
                'time_begin'  => $validated['time_begin'],
                'time_end'    => $validated['time_end'],
                'semester_id' => $validated['semester_id'],
            ]);

            // Update the parent period
            $period->update([
                'time_begin'  => $validated['time_begin'],
                'time_end'    => $validated['time_end'],
                'semester_id' => $validated['semester_id'],
            ]);

            DB::commit();
            return redirect()->route('periods.index', ['semester_id' => $validated['semester_id']])
                ->with('success', 'Lesson period updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating period: ' . $e->getMessage());
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
