<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Subject;
use App\Models\User;
use App\Models\Major;
use App\Models\LessonPeriod;
use App\Models\SchoolClass;
use App\Models\ActivityStudent;
use App\Models\SubjectAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    public function index(Request $request)
{
    try {
        $user = auth()->user();
        $showDeleted = $request->has('show_deleted') && in_array($user->role, ['ADMIN', 'VP']);
        
        $query = Activity::with('subject', 'teacher', 'period', 'class');

        $majorId = $request->query('major_id');
        if ($majorId) {
            $query->whereHas('class', function ($q) use ($majorId) {
                $q->where('major_id', $majorId);
            });
        }

        if ($showDeleted) {
            $activities = $query->onlyTrashed()->paginate(100);
        } else {
            $activities = $query->paginate(100);
        }

        $majors = Major::orderBy('name')->get();

        return view('activities.index', compact('activities', 'showDeleted', 'majors', 'majorId'));
    } catch (\Exception $e) {
        Log::error('Error loading activities: ' . $e->getMessage());
        return redirect()->back()->withErrors('Error loading activities: ' . $e->getMessage());
    }
}

    public function create()
    {
        try {
            $subjects = Subject::all();
            $teachers = User::where('role', '!=', 'STUDENT')->get();
            $periods = LessonPeriod::with('semester', 'major', 'grade')->get(); 
            $classes = SchoolClass::with('major', 'grade')->get();

            $classSubjects = [];
            foreach ($classes as $class) {
                $subjectIds = SubjectAvailability::where('major_id', $class->major_id)
                    ->where('grade_id', $class->grade_id)
                    ->pluck('subject_id')
                    ->toArray();
                $classSubjects[$class->id] = $subjectIds;
            }
            
            if ($subjects->isEmpty() || $teachers->isEmpty() || $periods->isEmpty() || $classes->isEmpty()) {
                return redirect()->route('activities.index')->withErrors('Missing required data. Ensure subjects, teachers, periods, and classes exist.');
            }
            
            return view('activities.create', compact('subjects', 'teachers', 'periods', 'classes', 'classSubjects'));
        } catch (\Exception $e) {
            Log::error('Error loading activity create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:users,id',
                'period_id'  => 'required|exists:lesson_periods,id',
                'class_id'   => 'required|exists:classes,id',
            ], [
                'subject_id.required' => 'Subject is required.',
                'subject_id.exists'   => 'Selected subject does not exist.',
                'teacher_id.required' => 'Teacher is required.',
                'teacher_id.exists'   => 'Selected teacher does not exist.',
                'period_id.required'  => 'Lesson period is required.',
                'period_id.exists'    => 'Selected period does not exist.',
                'class_id.required'   => 'Class is required.',
                'class_id.exists'     => 'Selected class does not exist.',
            ]);

            $period = LessonPeriod::find($validated['period_id']);
            $teacher = User::find($validated['teacher_id']);

            if (!in_array($teacher->role, ['TEACHER', 'VP', 'ADMIN'])) {
                return back()->withErrors(['teacher_id' => 'Selected user must be a teacher, VP, or admin.'])->withInput();
            }

            $teacherConflict = Activity::where('teacher_id', $validated['teacher_id'])
                ->whereHas('period', function ($query) use ($period) {
                    $query->where('semester_id', $period->semester_id)
                        ->where('weekday', $period->weekday)
                        ->where(function ($q) use ($period) {
                            $q->where('time_end', '>', $period->time_begin)
                              ->where('time_begin', '<', $period->time_end);
                        });
                })
                ->where('deleted_at', null)
                ->exists();

            if ($teacherConflict) {
                return back()->withErrors(['teacher_id' => 'Teacher has overlapping activities on this time slot.'])->withInput();
            }

            $classConflict = Activity::where('class_id', $validated['class_id'])
                ->whereHas('period', function ($query) use ($period) {
                    $query->where('semester_id', $period->semester_id)
                        ->where('weekday', $period->weekday)
                        ->where(function ($q) use ($period) {
                            $q->where('time_end', '>', $period->time_begin)
                              ->where('time_begin', '<', $period->time_end);
                        });
                })
                ->where('deleted_at', null)
                ->exists();

            if ($classConflict) {
                return back()->withErrors(['class_id' => 'Class has overlapping activities on this time slot.'])->withInput();
            }

            $duplicate = Activity::where('subject_id', $validated['subject_id'])
                ->where('teacher_id', $validated['teacher_id'])
                ->where('period_id', $validated['period_id'])
                ->where('class_id', $validated['class_id'])
                ->where('deleted_at', null)
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['subject_id' => 'This activity combination already exists.'])->withInput();
            }

            DB::beginTransaction();

            $activity = Activity::create($validated);

            $students = SchoolClass::find($validated['class_id'])
                ->students()
                ->where('role', 'STUDENT')
                ->where('deleted_at', null)
                ->pluck('users.id');

            foreach ($students as $studentId) {
                $activity->students()->attach($studentId);
            }

            DB::commit();

            return redirect()->route('activities.index')->with('success', 'Activity created successfully and enrolled to all class students.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating activity: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating activity: ' . $e->getMessage());
        }
    }

    public function show(Activity $activity)
    {
        try {
            $activity->load(['forms' => function ($q) {
                $q->with(['activity.period', 'presences' => function ($q) {
                    $q->where('student_id', auth()->id())->with('report');
                }]);
            }]);
            return view('activities.show', compact('activity'));
        } catch (\Exception $e) {
            Log::error('Error loading activity details: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading activity details: ' . $e->getMessage());
        }
    }

    public function edit(Activity $activity)
    {
        try {
            $subjects = Subject::all();
            $teachers = User::where('role', '!=', 'STUDENT')->get();
            $periods = LessonPeriod::with('semester', 'major', 'grade')->get();
            $classes = SchoolClass::with('major', 'grade')->get();

            $classSubjects = [];
            foreach ($classes as $class) {
                $subjectIds = SubjectAvailability::where('major_id', $class->major_id)
                    ->where('grade_id', $class->grade_id)
                    ->pluck('subject_id')
                    ->toArray();
                $classSubjects[$class->id] = $subjectIds;
            }

            return view('activities.edit', compact('activity', 'subjects', 'teachers', 'periods', 'classes', 'classSubjects'));
        } catch (\Exception $e) {
            Log::error('Error loading activity edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Activity $activity)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:users,id',
                'period_id'  => 'required|exists:lesson_periods,id',
                'class_id'   => 'required|exists:classes,id',
            ], [
                'subject_id.required' => 'Subject is required.',
                'subject_id.exists'   => 'Selected subject does not exist.',
                'teacher_id.required' => 'Teacher is required.',
                'teacher_id.exists'   => 'Selected teacher does not exist.',
                'period_id.required'  => 'Lesson period is required.',
                'period_id.exists'    => 'Selected period does not exist.',
                'class_id.required'   => 'Class is required.',
                'class_id.exists'     => 'Selected class does not exist.',
            ]);

            $period = LessonPeriod::find($validated['period_id']);
            $teacher = User::find($validated['teacher_id']);

            if (!in_array($teacher->role, ['TEACHER', 'VP', 'ADMIN'])) {
                return back()->withErrors(['teacher_id' => 'Selected user must be a teacher, VP, or admin.'])->withInput();
            }

            $teacherConflict = Activity::where('teacher_id', $validated['teacher_id'])
                ->where('id', '!=', $activity->id)
                ->whereHas('period', function ($query) use ($period) {
                    $query->where('semester_id', $period->semester_id)
                        ->where('weekday', $period->weekday)
                        ->where(function ($q) use ($period) {
                            $q->where('time_end', '>', $period->time_begin)
                              ->where('time_begin', '<', $period->time_end);
                        });
                })
                ->where('deleted_at', null)
                ->exists();

            if ($teacherConflict) {
                return back()->withErrors(['teacher_id' => 'Teacher has overlapping activities on this time slot.'])->withInput();
            }

            $classConflict = Activity::where('class_id', $validated['class_id'])
                ->where('id', '!=', $activity->id)
                ->whereHas('period', function ($query) use ($period) {
                    $query->where('semester_id', $period->semester_id)
                        ->where('weekday', $period->weekday)
                        ->where(function ($q) use ($period) {
                            $q->where('time_end', '>', $period->time_begin)
                              ->where('time_begin', '<', $period->time_end);
                        });
                })
                ->where('deleted_at', null)
                ->exists();

            if ($classConflict) {
                return back()->withErrors(['class_id' => 'Class has overlapping activities on this time slot.'])->withInput();
            }

            DB::beginTransaction();

            $classChanged = $activity->class_id !== $validated['class_id'];
            $activity->update($validated);

            if ($classChanged) {
                $activity->students()->detach();
                
                $students = SchoolClass::find($validated['class_id'])
                    ->students()
                    ->where('role', 'STUDENT')
                    ->where('deleted_at', null)
                    ->pluck('users.id');

                foreach ($students as $studentId) {
                    $activity->students()->attach($studentId);
                }
            }

            DB::commit();

            return redirect()->route('activities.index')->with('success', 'Activity updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating activity: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating activity: ' . $e->getMessage());
        }
    }

    public function destroy(Activity $activity)
    {
        DB::transaction(function () use ($activity) {
            ActivityStudent::where('activity_id', $activity->id)->delete();
            $activity->forms()->delete();
            $activity->presences()->delete();
            $activity->delete();
        });

        return redirect()->route('activities.index')->with('success', 'Activity deleted.');
    }

    public function restore(Activity $activity)
    {
        if (!in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
            return redirect()->route('wrongway');
        }

        DB::transaction(function () use ($activity) {
            $activity->restore();
            ActivityStudent::where('activity_id', $activity->id)->restore();
        });

        return redirect()->route('activities.index')->with('success', 'Activity restored.');
    }
}