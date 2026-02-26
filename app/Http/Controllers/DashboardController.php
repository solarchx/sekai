<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Activity;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $users = User::count();
        $classes = SchoolClass::count();
        $activities = Activity::count();

        // Get user's schedule (empty for VP and Admin)
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
        // VP and Admin do not have a personal schedule on the dashboard
        if (in_array($user->role, ['VP', 'ADMIN'])) {
            return [];
        }

        $activities = collect();

        if ($user->role === 'STUDENT' && $user->class_id) {
            $activities = Activity::where('class_id', $user->class_id)
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();
        } elseif ($user->role === 'TEACHER') {
            $activities = Activity::where('teacher_id', $user->id)
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();
        }

        // Group activities by weekday
        $schedule = [];
        foreach ($activities as $activity) {
            $weekday = $activity->period->weekday;
            $schedule[$weekday][] = $activity;
        }

        // Sort weekdays in ascending order (0 = Monday, 6 = Sunday)
        ksort($schedule);

        // Sort activities within each weekday by time_begin
        foreach ($schedule as &$weekdayActivities) {
            usort($weekdayActivities, function ($a, $b) {
                return strcmp($a->period->time_begin, $b->period->time_begin);
            });
        }

        return $schedule;
    }
}