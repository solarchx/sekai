<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index(): View
    {
        $user = auth()->user();

        return match ($user->role) {
            'ADMIN' => view('admin.admin-dashboard'),
            'VP' => view('vp.vp-dashboard'),
            'TEACHER' => view('teacher.teacher-dashboard'),
            'STUDENT' => view('student.student-dashboard'),
            default => view('student.student-dashboard'),
        };
    }
}
