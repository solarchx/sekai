<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AcademicSemesterController;
use App\Http\Controllers\LessonPeriodController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityFormController;
use App\Http\Controllers\ActivityPresenceController;
use App\Http\Controllers\ActivityReportController;
use App\Http\Controllers\ScoreDistributionController;
use App\Http\Controllers\StudentScoreController;
use App\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('majors', MajorController::class);
    Route::resource('classes', SchoolClassController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('grades', GradeController::class);
    Route::resource('semesters', AcademicSemesterController::class);
    Route::resource('periods', LessonPeriodController::class);
});

// VP and above routes
Route::middleware(['auth', 'vp'])->group(function () {
    Route::resource('activities', ActivityController::class);
});

// Teacher and above routes
Route::middleware(['auth', 'teacher'])->group(function () {
    Route::resource('activity-forms', ActivityFormController::class);
    Route::resource('activity-presences', ActivityPresenceController::class);
    Route::resource('score-distributions', ScoreDistributionController::class);
    Route::resource('student-scores', StudentScoreController::class);
});

// Student and above routes
Route::middleware(['auth'])->group(function () {
    Route::resource('activity-reports', ActivityReportController::class);
    Route::resource('announcements', AnnouncementController::class);
});

Route::get('/my-class', [SchoolClassController::class, 'show'])
    ->middleware('auth')
    ->name('class.show');

Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::get('/storylock', function () {
    return view('wrongway');
})->name('wrongway');

require __DIR__.'/auth.php';
