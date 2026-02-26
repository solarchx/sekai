<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        
        // Allow route model binding to find soft-deleted models
        Route::bind('user', function ($value) {
            return \App\Models\User::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('major', function ($value) {
            return \App\Models\Major::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('class', function ($value) {
            return \App\Models\SchoolClass::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('subject', function ($value) {
            return \App\Models\Subject::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('grade', function ($value) {
            return \App\Models\Grade::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('semester', function ($value) {
            return \App\Models\AcademicSemester::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('period', function ($value) {
            return \App\Models\LessonPeriod::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('activity', function ($value) {
            return \App\Models\Activity::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('activityForm', function ($value) {
            return \App\Models\ActivityForm::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('activityPresence', function ($value) {
            return \App\Models\ActivityPresence::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('activityReport', function ($value) {
            return \App\Models\ActivityReport::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('scoreDistribution', function ($value) {
            return \App\Models\ScoreDistribution::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('studentScore', function ($value) {
            return \App\Models\StudentScore::withTrashed()->where('id', $value)->firstOrFail();
        });
        
        Route::bind('announcement', function ($value) {
            return \App\Models\Announcement::withTrashed()->where('id', $value)->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
