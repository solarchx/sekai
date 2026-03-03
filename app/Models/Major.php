<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'majors';

    protected $fillable = [
        'name',
    ];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'major_id');
    }

    public function subjectAvailabilities()
    {
        return $this->hasMany(SubjectAvailability::class, 'major_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_availabilities', 'major_id', 'subject_id')
                    ->withPivot('grade_id')
                    ->withTimestamps();
    }

    public function subjectsForGrade($gradeId)
    {
        return $this->subjects()->wherePivot('grade_id', $gradeId);
    }

    public function lessonPeriods()
    {
        return $this->hasMany(LessonPeriod::class, 'major_id');
    }
}