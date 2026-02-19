<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lesson_periods';

    protected $fillable = [
        'weekday',
        'time_begin',
        'time_end',
        'semester_id',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'time_begin' => 'datetime:H:i',
        'time_end' => 'datetime:H:i',
    ];

    public function semester()
    {
        return $this->belongsTo(AcademicSemester::class, 'semester_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'period_id');
    }
}