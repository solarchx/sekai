<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Activity;
use App\Models\AcademicSemester;
use App\Models\LessonPeriod;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index(): View
    {
        $users = User::all()->count();
        $classes = SchoolClass::all()->count();
        $activities = Activity::all()->count();
        $user = auth()->user();

        // Get current or latest academic semester
        $currentSemester = AcademicSemester::latest()->first();

        // Get user-specific activities and periods
        $parentPeriods = collect();
        $periods = collect();
        
        if ($currentSemester) {
            if ($user->role === 'STUDENT') {
                // Get activities for student's class
                $parentPeriods = LessonPeriod::with('semester')
                    ->where('semester_id', $currentSemester->id)
                    ->whereNull('parent_id')
                    ->get();
                
                $periods = LessonPeriod::with(['activities' => function($q) use ($user) {
                    $q->with('subject', 'teacher', 'class')
                      ->where('class_id', $user->class_id)
                      ->where('deleted_at', null);
                }])
                    ->where('semester_id', $currentSemester->id)
                    ->get();
            } elseif ($user->role === 'TEACHER') {
                // Get activities taught by this teacher
                $parentPeriods = LessonPeriod::with('semester')
                    ->where('semester_id', $currentSemester->id)
                    ->whereNull('parent_id')
                    ->get();
                
                $periods = LessonPeriod::with(['activities' => function($q) use ($user) {
                    $q->with('subject', 'teacher', 'class')
                      ->where('teacher_id', $user->id)
                      ->where('deleted_at', null);
                }])
                    ->where('semester_id', $currentSemester->id)
                    ->get();
            }
        }

        return match ($user->role) {
            'ADMIN' => view('admin.admin-dashboard', compact('users', 'classes', 'activities')),
            'VP' => view('vp.vp-dashboard'),
            'TEACHER' => view('teacher.teacher-dashboard', compact('parentPeriods', 'periods', 'currentSemester')),
            'STUDENT' => view('student.student-dashboard', compact('parentPeriods', 'periods', 'currentSemester')),
            default => view('student.student-dashboard', compact('parentPeriods', 'periods', 'currentSemester')),
        };
    }
}
