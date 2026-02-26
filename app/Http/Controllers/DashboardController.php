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
    public function index(): View
    {
        $user = auth()->user();
        $users = User::count();
        $classes = SchoolClass::count();
        $activities = Activity::count();

        // Get user's schedule
        $schedule = $this->getUserSchedule($user);

        return match ($user->role) {
            'ADMIN'   => view('admin.admin-dashboard', compact('users', 'classes', 'activities', 'schedule')),
            'VP'      => view('vp.vp-dashboard', compact('schedule')),
            'TEACHER' => view('teacher.teacher-dashboard', compact('schedule')),
            'STUDENT' => view('student.student-dashboard', compact('schedule')),
            default   => view('student.student-dashboard', compact('schedule')),
        };
    }

    /**
     * Get the schedule (activities) for a given user.
     */
    private function getUserSchedule(User $user)
    {
        $activities = collect();

        if ($user->role === 'STUDENT' && $user->class_id) {
            $activities = Activity::where('class_id', $user->class_id)
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();
        } else {
            $activities = Activity::where('teacher_id', $user->id)
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();
        }

        // Group activities by weekday and time slot for easier display
        $schedule = [];
        foreach ($activities as $activity) {
            $weekday = $activity->period->weekday;
            $timeSlot = $activity->period->time_begin . '-' . $activity->period->time_end;
            $schedule[$weekday][$timeSlot][] = $activity;
        }

        return $schedule;
    }
}