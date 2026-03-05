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
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DataTransferController;
use Illuminate\Support\Facades\Route;


Route::get('/',  function () {return view('auth/login');})->middleware('guest');

Route::get('/language/{language}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'vp'])->group(function () {
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{user}/force', [UserController::class, 'forceDestroy'])->name('users.force-destroy');

    Route::resource('majors', MajorController::class);
    Route::post('/majors/{major}/restore', [MajorController::class, 'restore'])->name('majors.restore');
    Route::delete('/majors/{major}/force', [MajorController::class, 'forceDestroy'])->name('majors.force-destroy');

    Route::resource('classes', SchoolClassController::class);
    Route::post('/classes/{class}/restore', [SchoolClassController::class, 'restore'])->name('classes.restore');
    Route::delete('/classes/{class}/force', [SchoolClassController::class, 'forceDestroy'])->name('classes.force-destroy');

    Route::resource('subjects', SubjectController::class);
    Route::post('/subjects/{subject}/restore', [SubjectController::class, 'restore'])->name('subjects.restore');
    Route::delete('/subjects/{subject}/force', [SubjectController::class, 'forceDestroy'])->name('subjects.force-destroy');

    Route::resource('grades', GradeController::class);
    Route::post('/grades/{grade}/restore', [GradeController::class, 'restore'])->name('grades.restore');
    Route::delete('/grades/{grade}/force', [GradeController::class, 'forceDestroy'])->name('grades.force-destroy');

    Route::resource('semesters', AcademicSemesterController::class);
    Route::post('/semesters/{semester}/restore', [AcademicSemesterController::class, 'restore'])->name('semesters.restore');
    Route::delete('/semesters/{semester}/force', [AcademicSemesterController::class, 'forceDestroy'])->name('semesters.force-destroy');

    Route::resource('periods', LessonPeriodController::class);
    Route::post('/periods/{period}/restore', [LessonPeriodController::class, 'restore'])->name('periods.restore');
    Route::delete('/periods/{period}/force', [LessonPeriodController::class, 'forceDestroy'])->name('periods.force-destroy');

    Route::resource('activities', ActivityController::class);
    Route::post('/activities/{activity}/restore', [ActivityController::class, 'restore'])->name('activities.restore');
    Route::delete('/activities/{activity}/force', [ActivityController::class, 'forceDestroy'])->name('activities.force-destroy');

    Route::get('/dashboard/export', [DataTransferController::class, 'export'])->name('dashboard.export');
    Route::get('/dashboard/template', [DataTransferController::class, 'downloadTemplate'])->name('dashboard.template');
    Route::post('/dashboard/import', [DataTransferController::class, 'import'])->name('dashboard.import');
});

Route::middleware(['auth', 'teacher'])->group(function () {
    Route::resource('activity-forms', ActivityFormController::class);
    Route::post('/activity-forms/{activityForm}/restore', [ActivityFormController::class, 'restore'])->name('activity-forms.restore');
    Route::get('/activity-presences', [ActivityPresenceController::class, 'index'])->name('activity-presences.index');
    Route::get('/activity-forms/{activityForm}/presences/{activityPresence}/edit', [ActivityPresenceController::class, 'edit'])->name('activity-presences.edit');
    Route::put('/activity-forms/{activityForm}/presences/{activityPresence}', [ActivityPresenceController::class, 'update'])->name('activity-presences.update');
    Route::delete('/activity-forms/{activityForm}/presences/{activityPresence}', [ActivityPresenceController::class, 'destroy'])->name('activity-presences.destroy');
    Route::post('/activity-forms/{activityForm}/presences/{activityPresence}/restore', [ActivityPresenceController::class, 'restore'])->name('activity-presences.restore');
    Route::delete('/activity-forms/{activityForm}/force', [ActivityFormController::class, 'forceDestroy'])->name('activity-forms.force-destroy');
    Route::get('/activities/{activity}/score-distributions', [ScoreDistributionController::class, 'index'])->name('score-distributions.index');
    Route::get('/activities/{activity}/score-distributions/create', [ScoreDistributionController::class, 'create'])->name('score-distributions.create');
    Route::post('/activities/{activity}/score-distributions', [ScoreDistributionController::class, 'store'])->name('score-distributions.store');
    Route::get('/activities/{activity}/score-distributions/edit', [ScoreDistributionController::class, 'edit'])->name('score-distributions.edit');
    Route::get('/activities/{activity}/student-scores', [StudentScoreController::class, 'index'])->name('student-scores.index');
    Route::get('/activities/{activity}/student-scores/{student}/edit', [StudentScoreController::class, 'edit'])->name('student-scores.edit');
    Route::put('/activities/{activity}/student-scores/{student}', [StudentScoreController::class, 'update'])->name('student-scores.update');
    Route::get('/classes/{class}/student-order', [SchoolClassController::class, 'studentOrder'])->name('classes.student-order');
    Route::put('/classes/{class}/student-order', [SchoolClassController::class, 'updateStudentOrder'])->name('classes.update-student-order');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/student/activities', [App\Http\Controllers\StudentActivityController::class, 'index'])->middleware('auth')->name('student.activities');
    Route::get('/student/grades', [App\Http\Controllers\StudentActivityController::class, 'grades'])->middleware('auth')->name('student.grades');
    Route::resource('activity-reports', ActivityReportController::class);
    Route::post('/activity-reports/{activityReport}/restore', [ActivityReportController::class, 'restore'])->name('activity-reports.restore');
    Route::resource('announcements', AnnouncementController::class);
    Route::post('/announcements/{announcement}/restore', [AnnouncementController::class, 'restore'])->name('announcements.restore');
    Route::get('/activity-presences/create', [ActivityPresenceController::class, 'create'])->name('activity-presences.create');
    Route::post('/activity-presences', [ActivityPresenceController::class, 'store'])->name('activity-presences.store');
});

Route::get('/my-class', [SchoolClassController::class, 'show'])->middleware('auth')->name('class.show');

Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::get('/storylock', function () {
    return view('wrongway');
})->name('wrongway');

require __DIR__.'/auth.php';
