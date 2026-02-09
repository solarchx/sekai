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
            'ADMIN' => view('dashboards.admin-dashboard'),
            'VP' => view('dashboards.vp-dashboard'),
            'TEACHER' => view('dashboards.teacher-dashboard'),
            'STUDENT' => view('dashboards.student-dashboard'),
            default => view('dashboards.student-dashboard'),
        };
    }
}
