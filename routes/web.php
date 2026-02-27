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


Route::get('/',  function () {return view('auth/login');})->middleware('guest');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    
    Route::resource('majors', MajorController::class);
    Route::post('/majors/{major}/restore', [MajorController::class, 'restore'])->name('majors.restore');
    
    Route::resource('classes', SchoolClassController::class);
    Route::post('/classes/{class}/restore', [SchoolClassController::class, 'restore'])->name('classes.restore');
    Route::get('/classes/{class}/student-order', [SchoolClassController::class, 'studentOrder'])->name('classes.student-order');
    Route::put('/classes/{class}/student-order', [SchoolClassController::class, 'updateStudentOrder'])->name('classes.update-student-order');
    
    Route::resource('subjects', SubjectController::class);
    Route::post('/subjects/{subject}/restore', [SubjectController::class, 'restore'])->name('subjects.restore');
    
    Route::resource('grades', GradeController::class);
    Route::post('/grades/{grade}/restore', [GradeController::class, 'restore'])->name('grades.restore');
    
    Route::resource('semesters', AcademicSemesterController::class);
    Route::post('/semesters/{semester}/restore', [AcademicSemesterController::class, 'restore'])->name('semesters.restore');
    
    Route::resource('periods', LessonPeriodController::class);
    Route::post('/periods/{period}/restore', [LessonPeriodController::class, 'restore'])->name('periods.restore');
});


Route::middleware(['auth', 'vp'])->group(function () {
    Route::resource('activities', ActivityController::class);
    Route::post('/activities/{activity}/restore', [ActivityController::class, 'restore'])->name('activities.restore');
});


Route::middleware(['auth', 'teacher'])->group(function () {
    Route::resource('activity-forms', ActivityFormController::class);
    Route::post('/activity-forms/{activityForm}/restore', [ActivityFormController::class, 'restore'])->name('activity-forms.restore');
    Route::resource('activity-presences', ActivityPresenceController::class);
    Route::post('/activity-presences/{activityPresence}/restore', [ActivityPresenceController::class, 'restore'])->name('activity-presences.restore');
    Route::resource('score-distributions', ScoreDistributionController::class);
    Route::post('/score-distributions/{scoreDistribution}/restore', [ScoreDistributionController::class, 'restore'])->name('score-distributions.restore');
    Route::resource('student-scores', StudentScoreController::class);
    Route::post('/student-scores/{studentScore}/restore', [StudentScoreController::class, 'restore'])->name('student-scores.restore');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('activity-reports', ActivityReportController::class);
    Route::post('/activity-reports/{activityReport}/restore', [ActivityReportController::class, 'restore'])->name('activity-reports.restore');
    Route::resource('announcements', AnnouncementController::class);
    Route::post('/announcements/{announcement}/restore', [AnnouncementController::class, 'restore'])->name('announcements.restore');
});

Route::get('/my-class', [SchoolClassController::class, 'myClasses'])->middleware('auth')->name('class.show');

Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::get('/storylock', function () {
    return view('wrongway');
})->name('wrongway');

require __DIR__.'/auth.php';
