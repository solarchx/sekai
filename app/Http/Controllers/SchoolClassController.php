<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\Grade;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
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

            // Validate homeroom teacher is not a student
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

    /**
     * Display "My Class" page for students.
     */
    public function show(SchoolClass $class)
    {
        try {
            $userClass = Auth::user()->class()->with(['major', 'grade'])->first();
            
            if (!$userClass) {
                return redirect()->route('dashboard')->withErrors('You are not assigned to any class.');
            }

            $homeroomTeacher = null;
            if ($userClass->homeroom_teacher_id) {
                $homeroomTeacher = User::find($userClass->homeroom_teacher_id);
            }

            $students = $userClass->students()
                ->where('role', 'STUDENT')
                ->where('deleted_at', null)
                ->orderBy('name')
                ->get();

            // Get activities for the class
            $activities = $userClass->activities()
                ->with('teacher', 'subject', 'period')
                ->where('deleted_at', null)
                ->get();

            return view('class.show', compact('userClass', 'homeroomTeacher', 'students', 'activities'));
        } catch (Exception $e) {
            Log::error('Error loading my class: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Error loading class: ' . $e->getMessage());
        }
    }

    /**
     * Show student order management page.
     */
    public function studentOrder(SchoolClass $class)
    {
        try {
            if (auth()->user()->role !== 'ADMIN' && auth()->user()->role !== 'VP') {
                return redirect()->back()->withErrors('You do not have permission to reorder students.');
            }

            $students = $class->students()
                ->where('role', 'STUDENT')
                ->where('deleted_at', null)
                ->withPivot('student_order')
                ->orderBy('pivot_student_order')
                ->get();

            return view('admin.classes.student-order', compact('class', 'students'));
        } catch (\Exception $e) {
            Log::error('Error loading student order: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading student order: ' . $e->getMessage());
        }
    }

    /**
     * Update student order for a class.
     */
    public function updateStudentOrder(Request $request, SchoolClass $class)
    {
        try {
            if (auth()->user()->role !== 'ADMIN' && auth()->user()->role !== 'VP') {
                return back()->withErrors('You do not have permission to reorder students.');
            }

            $validated = $request->validate([
                'student_orders' => 'required|array',
                'student_orders.*' => 'required|integer|min:1',
            ], [
                'student_orders.required' => 'Student orders are required.',
                'student_orders.*.required' => 'Each student must have an order number.',
                'student_orders.*.integer' => 'Order numbers must be integers.',
                'student_orders.*.min' => 'Order numbers must be at least 1.',
            ]);

            DB::beginTransaction();

            // Update student order in activity_students pivot table
            foreach ($validated['student_orders'] as $studentId => $order) {
                $student = User::find($studentId);
                if ($student && $student->class_id === $class->id && $student->role === 'STUDENT') {
                    // Update all activities for this student in this class
                    DB::table('activity_students')
                        ->join('activities', 'activity_students.activity_id', '=', 'activities.id')
                        ->where('activity_students.student_id', $studentId)
                        ->where('activities.class_id', $class->id)
                        ->update(['activity_students.student_order' => $order]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Student order updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating student order: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating order: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
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

            // Validate homeroom teacher is not a student
            if (isset($validated['homeroom_teacher_id'])) {
                $teacher = User::find($validated['homeroom_teacher_id']);
                if ($teacher->role === 'STUDENT') {
                    return back()->withErrors(['homeroom_teacher_id' => 'Homeroom teacher cannot be a student.'])->withInput();
                }
            }

            // Check if capacity reduction would violate current enrollment
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $class)
    {
        try {
            DB::beginTransaction();

            // Remove all students from the class (soft delete their associations)
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

    /**
     * Restore a soft-deleted class (admin only).
     */
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
}
