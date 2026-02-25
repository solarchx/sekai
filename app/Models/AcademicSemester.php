<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSemester extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'academic_semesters';

    protected $fillable = [
        'academic_year',
        'semester',
    ];

    protected $casts = [
        'semester' => 'integer',
    ];

    public function lessonPeriods()
    {
        return $this->hasMany(LessonPeriod::class, 'semester_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->academic_year . ' Semester ' . $this->semester;
    }
}