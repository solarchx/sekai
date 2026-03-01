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

        
        $schedule = $this->getUserSchedule($user);

        return match ($user->role) {
            'ADMIN'   => view('admin.admin-dashboard', compact('users', 'classes', 'activities', 'schedule')),
            'VP'      => view('vp.vp-dashboard', compact('schedule')),
            'TEACHER' => view('teacher.teacher-dashboard', compact('schedule')),
            'STUDENT' => view('student.student-dashboard', compact('schedule')),
            default   => view('student.student-dashboard', compact('schedule')),
        };
    }

    
    private function getUserSchedule(User $user)
    {
        $activities = collect();

        if ($user->role === 'STUDENT' && $user->class_id) {
            $activities = Activity::where('class_id', $user->class_id)
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();
        } elseif (in_array($user->role, ['TEACHER', 'VP', 'ADMIN'])) {
            $activities = Activity::where('teacher_id', $user->id)
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();
        }

        
        $schedule = [];
        foreach ($activities as $activity) {
            $weekday = $activity->period->weekday;
            $schedule[$weekday][] = $activity;
        }

        
        ksort($schedule);

        
        foreach ($schedule as &$weekdayActivities) {
            usort($weekdayActivities, function ($a, $b) {
                return strcmp($a->period->time_begin, $b->period->time_begin);
            });
        }

        return $schedule;
    }
}