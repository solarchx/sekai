<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\Grade;
use App\Models\Activity;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolClassController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = SchoolClass::with('major', 'grade', 'homeroomTeacher');
            
            if ($showDeleted) {
                $classes = $query->onlyTrashed()->paginate(100);
            } else {
                $classes = $query->paginate(100);
            }
            
            return view('admin.classes.index', compact('classes', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading classes: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading classes: ' . $e->getMessage());
        }
    }

    
    public function create()
    {
        try {
            $majors = Major::all();
            $grades = Grade::all();
            $teachers = User::where('role', '!=', 'STUDENT')->get();
            
            if ($majors->isEmpty() || $grades->isEmpty()) {
                return redirect()->route('classes.index')->withErrors('Missing required data. Ensure majors and grades exist.');
            }
            
            return view('admin.classes.create', compact('majors', 'grades', 'teachers'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'major_id' => 'required|exists:majors,id',
                'grade_id' => 'required|exists:grades,id',
                'capacity' => 'required|integer|min:1|max:100',
                'homeroom_teacher_id' => 'nullable|exists:users,id',
            ], [
                'name.required' => 'Class name is required.',
                'major_id.required' => 'Major is required.',
                'major_id.exists' => 'Selected major does not exist.',
                'grade_id.required' => 'Grade is required.',
                'grade_id.exists' => 'Selected grade does not exist.',
                'capacity.required' => 'Capacity is required.',
                'capacity.integer' => 'Capacity must be a number.',
                'capacity.min' => 'Capacity must be at least 1.',
                'capacity.max' => 'Capacity cannot exceed 100.',
                'homeroom_teacher_id.exists' => 'Selected teacher does not exist.',
            ]);

            
            if (isset($validated['homeroom_teacher_id'])) {
                $teacher = User::find($validated['homeroom_teacher_id']);
                if ($teacher->role === 'STUDENT') {
                    return back()->withErrors(['homeroom_teacher_id' => 'Homeroom teacher cannot be a student.'])->withInput();
                }
            }

            DB::beginTransaction();

            SchoolClass::create($validated);

            DB::commit();

            return redirect()->route('classes.index')->with('success', 'Class created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating class: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating class: ' . $e->getMessage());
        }
    }

    public function updateStudentOrder(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'student_orders' => 'required|array',
            'student_orders.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        foreach ($validated['student_orders'] as $studentId => $order) {
            User::where('id', $studentId)
                ->where('class_id', $class->id)
                ->where('role', 'STUDENT')
                ->update(['student_order' => $order]);
        }
        DB::commit();

        return redirect()->back()->with('success', 'Student order updated.');
    }

    
    public function edit(SchoolClass $class)
    {
        try {
            $majors = Major::all();
            $grades = Grade::all();
            $teachers = User::where('role', '!=', 'STUDENT')
                ->where('deleted_at', null)
                ->get();

            return view('admin.classes.edit', compact('class', 'majors', 'grades', 'teachers'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    
    public function update(Request $request, SchoolClass $class)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'major_id' => 'required|exists:majors,id',
                'grade_id' => 'required|exists:grades,id',
                'capacity' => 'required|integer|min:1|max:100',
                'homeroom_teacher_id' => 'nullable|exists:users,id',
            ], [
                'name.required' => 'Class name is required.',
                'major_id.required' => 'Major is required.',
                'major_id.exists' => 'Selected major does not exist.',
                'grade_id.required' => 'Grade is required.',
                'grade_id.exists' => 'Selected grade does not exist.',
                'capacity.required' => 'Capacity is required.',
                'capacity.integer' => 'Capacity must be a number.',
                'capacity.min' => 'Capacity must be at least 1.',
                'capacity.max' => 'Capacity cannot exceed 100.',
                'homeroom_teacher_id.exists' => 'Selected teacher does not exist.',
            ]);

            
            if (isset($validated['homeroom_teacher_id'])) {
                $teacher = User::find($validated['homeroom_teacher_id']);
                if ($teacher->role === 'STUDENT') {
                    return back()->withErrors(['homeroom_teacher_id' => 'Homeroom teacher cannot be a student.'])->withInput();
                }
            }

            
            $currentStudentCount = $class->students()
                ->where('role', 'STUDENT')
                ->count();

            if ($validated['capacity'] < $currentStudentCount) {
                return back()->withErrors(['capacity' => "Class currently has {$currentStudentCount} students. Capacity cannot be less than this."])->withInput();
            }

            DB::beginTransaction();

            $class->update($validated);

            DB::commit();

            return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating class: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating class: ' . $e->getMessage());
        }
    }

    
    public function destroy(SchoolClass $class)
    {
        try {
            DB::beginTransaction();

            
            $class->students()->update(['class_id' => null]);

            $class->delete();

            DB::commit();

            return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting class: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting class: ' . $e->getMessage());
        }
    }

    
    public function restore(SchoolClass $class)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $class->restore();

            return redirect()->route('classes.index')->with('success', 'Class restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring class: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring class: ' . $e->getMessage());
        }
    }

    public function studentOrder(SchoolClass $class)
    {
        $students = $class->students()
            ->where('role', 'STUDENT')
            ->where('deleted_at', null)
            ->orderBy('student_order')
            ->get(['id', 'name', 'identifier', 'student_order']);  

        return view('admin.classes.student-order', compact('class', 'students'));
    }

    public function show()
    {
        try {
            $user = auth()->user();
            $homeroomClasses = collect();
            $taughtActivities = collect();

            if ($user->role === 'STUDENT') {
                
                if ($user->class_id) {
                    $class = SchoolClass::with('major', 'grade')->find($user->class_id);
                    if ($class) {
                        $class->students = $class->students()
                            ->where('role', 'STUDENT')
                            ->where('deleted_at', null)
                            ->orderBy('student_order')
                            ->get(['id', 'name', 'identifier', 'student_order']);
                        $class->homeroomTeacher = $class->homeroom_teacher_id ? User::find($class->homeroom_teacher_id) : null;
                        $class->activities = $class->activities()
                            ->with(['subject', 'teacher', 'period'])
                            ->where('deleted_at', null)
                            ->get();
                        $homeroomClasses->push($class);
                    }
                }
            } else {
                
                $homeroomClasses = SchoolClass::with('major', 'grade')
                    ->where('homeroom_teacher_id', $user->id)
                    ->get()
                    ->map(function ($class) {
                        $class->homeroomTeacher = $class->homeroom_teacher_id ? User::find($class->homeroom_teacher_id) : null;
                        
                        $class->students = $class->students()
                            ->where('role', 'STUDENT')
                            ->where('deleted_at', null)
                            ->orderBy('student_order')
                            ->get(['id', 'name', 'identifier', 'student_order']);
                        $class->activities = $class->activities()
                            ->with(['subject', 'teacher', 'period'])
                            ->where('deleted_at', null)
                            ->get();
                        return $class;
                    });

                
                $taughtActivities = Activity::with(['subject', 'teacher', 'class.major', 'class.grade', 'period'])
                    ->where('teacher_id', $user->id)
                    ->where('deleted_at', null)
                    ->get()
                    ->sortBy(function ($activity) {
                        return $activity->class->name . ' ' . $activity->period->weekday . ' ' . $activity->period->time_begin;
                    });
            }

            return view('class.show', compact('homeroomClasses', 'taughtActivities'));
        } catch (\Exception $e) {
            Log::error('Error loading my classes: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Error loading classes: ' . $e->getMessage());
        }
    }
}
