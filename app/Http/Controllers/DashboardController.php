<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Activity;

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

        return match ($user->role) {
            'ADMIN' => view('admin.admin-dashboard', compact('users', 'classes', 'activities')),
            'VP' => view('vp.vp-dashboard'),
            'TEACHER' => view('teacher.teacher-dashboard'),
            'STUDENT' => view('student.student-dashboard'),
            default => view('student.student-dashboard'),
        };
    }
}
