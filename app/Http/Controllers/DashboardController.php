<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Activity;
use App\Models\ActivityReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $users = User::count();
        $classes = SchoolClass::count();
        $activities = Activity::count();

        $schedule = $this->getUserSchedule($user);

        $reportRange = $request->query('report_range', 'week');
        $reportStats = $this->getReportStats($reportRange);

        return match ($user->role) {
            'ADMIN'   => view('admin.admin-dashboard', compact('users', 'classes', 'activities', 'schedule', 'reportStats', 'reportRange')),
            'VP'      => view('vp.vp-dashboard', compact('schedule', 'reportStats', 'reportRange')),
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

        $activities = $activities->filter(function ($activity) {
            return $activity->period !== null;
        });

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

    private function getReportStats($range)
    {
        $now = Carbon::now();

        if ($range === 'week') {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
        } else {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
        }

        $stats = ActivityReport::join('activity_presences', 'activity_reports.presence_id', '=', 'activity_presences.id')
            ->join('activity_forms', 'activity_presences.form_id', '=', 'activity_forms.id')
            ->join('activities', 'activity_forms.activity_id', '=', 'activities.id')
            ->join('lesson_periods', 'activities.period_id', '=', 'lesson_periods.id')
            ->select(DB::raw('lesson_periods.weekday, count(*) as count'))
            ->whereBetween('activity_forms.activity_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('lesson_periods.weekday')
            ->orderBy('lesson_periods.weekday')
            ->pluck('count', 'weekday');

        $weekdayNames = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $result = [];
        for ($i = 0; $i <= 6; $i++) {
            $result[$i] = [
                'weekday' => $weekdayNames[$i],
                'count' => $stats[$i] ?? 0,
            ];
        }

        return $result;
    }
}