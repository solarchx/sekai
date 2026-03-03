<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grades';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
    ];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'grade_id');
    }

    public function subjectAvailabilities()
    {
        return $this->hasMany(SubjectAvailability::class, 'grade_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_availabilities', 'grade_id', 'subject_id')
                    ->withPivot('major_id')
                    ->withTimestamps();
    }

    public function subjectsForMajor($majorId)
    {
        return $this->subjects()->wherePivot('major_id', $majorId);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'grade_id');
    }

    public function lessonPeriods()
    {
        return $this->hasMany(LessonPeriod::class, 'grade_id');
    }
}