<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activities';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'period_id',
        'class_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function period()
    {
        return $this->belongsTo(LessonPeriod::class, 'period_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'activity_students', 'activity_id', 'student_id')
                    ->withTimestamps()
                    ->withPivot('student_order');
    }

    public function activityForms()
    {
        return $this->hasMany(ActivityForm::class, 'activity_id');
    }

    public function forms()
    {
        return $this->hasMany(ActivityForm::class, 'activity_id');
    }

    public function scoreDistributions()
    {
        return $this->hasMany(ScoreDistribution::class, 'activity_id');
    }

    public function studentScores()
    {
        return $this->hasMany(StudentScore::class, 'activity_id');
    }

    /**
     * Check if teacher has overlapping activities
     */
    public function hasTeacherOverlap(): bool
    {
        $conflictingActivities = Activity::where('teacher_id', $this->teacher_id)
            ->where('id', '!=', $this->id)
            ->whereHas('period', function ($query) {
                $query->where('semester_id', $this->period->semester_id)
                    ->where('weekday', $this->period->weekday)
                    ->where(function ($q) {
                        $thisStart = $this->period->time_begin;
                        $thisEnd = $this->period->time_end;
                        $q->where('time_end', '>', $thisStart)
                          ->where('time_begin', '<', $thisEnd);
                    });
            })
            ->exists();

        return $conflictingActivities;
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'activity_id');
    }
}