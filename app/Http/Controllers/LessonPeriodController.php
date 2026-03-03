<?php

namespace App\Http\Controllers;

use App\Models\LessonPeriod;
use App\Models\AcademicSemester;
use App\Models\Activity;
use App\Models\Major;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonPeriodController extends Controller
{
    public function index(Request $request)
    {
        try {
            $semesters = AcademicSemester::all();
            $majors = Major::all();
            $grades = Grade::all();

            $selectedSemesterId = $request->query('semester_id');
            $selectedMajorId = $request->query('major_id');
            $selectedGradeId = $request->query('grade_id');

            $parentPeriods = collect();
            $periods = collect();
            $activities = collect();

            if ($selectedSemesterId && $selectedMajorId && $selectedGradeId) {
                $parentPeriods = LessonPeriod::with('semester', 'major', 'grade')->withTrashed()
                    ->where('semester_id', $selectedSemesterId)
                    ->where('major_id', $selectedMajorId)
                    ->where('grade_id', $selectedGradeId)
                    ->whereNull('parent_id')
                    ->orderBy('time_begin')
                    ->get();

                $periods = LessonPeriod::withTrashed()
                    ->where('semester_id', $selectedSemesterId)
                    ->where('major_id', $selectedMajorId)
                    ->where('grade_id', $selectedGradeId)
                    ->get();

                $activities = Activity::whereIn('period_id', $periods->pluck('id'))
                    ->with(['subject', 'teacher', 'class'])
                    ->get()
                    ->groupBy('period_id');
            }

            return view('periods.schedule-sheet', compact(
                'semesters', 'majors', 'grades',
                'selectedSemesterId', 'selectedMajorId', 'selectedGradeId',
                'parentPeriods', 'periods', 'activities'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading periods: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading periods: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $semesters = AcademicSemester::all();
            $majors = Major::all();
            $grades = Grade::all();

            $selectedSemesterId = $request->query('semester_id');
            $selectedMajorId = $request->query('major_id');
            $selectedGradeId = $request->query('grade_id');

            if (!$selectedSemesterId || !$selectedMajorId || !$selectedGradeId) {
                return redirect()->route('periods.index')
                    ->withErrors('Semester, major, and grade must be selected to create a period.');
            }

            return view('periods.create', compact('semesters', 'majors', 'grades', 'selectedSemesterId', 'selectedMajorId', 'selectedGradeId'));
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
                'major_id'    => 'required|exists:majors,id',
                'grade_id'    => 'required|exists:grades,id',
            ]);

            DB::beginTransaction();

            $parentPeriod = null;

            for ($day = 0; $day <= 6; $day++) {
                $overlapping = LessonPeriod::where('weekday', $day)
                    ->where('semester_id', $validated['semester_id'])
                    ->where('major_id', $validated['major_id'])
                    ->where('grade_id', $validated['grade_id'])
                    ->whereNull('parent_id')
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

                $currentPeriod = LessonPeriod::create([
                    'weekday'     => $day,
                    'time_begin'  => $validated['time_begin'],
                    'time_end'    => $validated['time_end'],
                    'semester_id' => $validated['semester_id'],
                    'major_id'    => $validated['major_id'],
                    'grade_id'    => $validated['grade_id'],
                    'parent_id'   => $parentPeriod ? $parentPeriod->id : null,
                ]);

                if ($day == 0) {
                    $parentPeriod = $currentPeriod;
                }
            }

            DB::commit();

            return redirect()->route('periods.index', [
                'semester_id' => $validated['semester_id'],
                'major_id'    => $validated['major_id'],
                'grade_id'    => $validated['grade_id'],
            ])->with('success', 'Lesson period created successfully for all 7 days.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating period: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating period: ' . $e->getMessage());
        }
    }

    public function edit(LessonPeriod $period)
    {
        try {
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only edit parent periods directly.');
            }

            $semesters = AcademicSemester::all();
            $majors = Major::all();
            $grades = Grade::all();

            return view('periods.edit', compact('period', 'semesters', 'majors', 'grades'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LessonPeriod $period)
    {
        try {
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only edit parent periods directly.');
            }

            $validated = $request->validate([
                'time_begin'  => 'required|date_format:H:i',
                'time_end'    => 'required|date_format:H:i|after:time_begin',
                'semester_id' => 'required|exists:academic_semesters,id',
                'major_id'    => 'required|exists:majors,id',
                'grade_id'    => 'required|exists:grades,id',
            ]);

            DB::beginTransaction();

            $overlapping = LessonPeriod::where('semester_id', $validated['semester_id'])
                ->where('major_id', $validated['major_id'])
                ->where('grade_id', $validated['grade_id'])
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

            $period->childPeriods()->update([
                'time_begin'  => $validated['time_begin'],
                'time_end'    => $validated['time_end'],
                'semester_id' => $validated['semester_id'],
                'major_id'    => $validated['major_id'],
                'grade_id'    => $validated['grade_id'],
            ]);

            $period->update([
                'time_begin'  => $validated['time_begin'],
                'time_end'    => $validated['time_end'],
                'semester_id' => $validated['semester_id'],
                'major_id'    => $validated['major_id'],
                'grade_id'    => $validated['grade_id'],
            ]);

            DB::commit();

            return redirect()->route('periods.index', [
                'semester_id' => $validated['semester_id'],
                'major_id'    => $validated['major_id'],
                'grade_id'    => $validated['grade_id'],
            ])->with('success', 'Lesson period updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating period: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating period: ' . $e->getMessage());
        }
    }

    public function destroy(LessonPeriod $period)
    {
        try {
            if ($period->parent_id !== null) {
                return redirect()->route('periods.index')->withErrors('You can only delete parent periods directly.');
            }

            DB::beginTransaction();

            $period->childPeriods()->delete();
            $period->delete();

            DB::commit();

            return redirect()->route('periods.index', [
                'semester_id' => $period->semester_id,
                'major_id'    => $period->major_id,
                'grade_id'    => $period->grade_id,
            ])->with('success', 'Lesson period deleted successfully (all 7 days removed).');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Error deleting period: ' . $e->getMessage());
        }
    }

    public function restore(LessonPeriod $period)
    {
        if (!in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
            return redirect()->route('wrongway');
        }

        DB::transaction(function () use ($period) {
            $period->restore();
            $period->childPeriods()->restore();
        });

        return redirect()->route('periods.index', [
            'semester_id' => $period->semester_id,
            'major_id'    => $period->major_id,
            'grade_id'    => $period->grade_id,
        ])->with('success', 'Period restored.');
    }
}