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
        'parent_id',
    ];

    protected $casts = [
        'weekday' => 'integer',
    ];

    const WEEKDAYS = [
        0 => 'Monday',
        1 => 'Tuesday',
        2 => 'Wednesday',
        3 => 'Thursday',
        4 => 'Friday',
        5 => 'Saturday',
        6 => 'Sunday',
    ];

    public function semester()
    {
        return $this->belongsTo(AcademicSemester::class, 'semester_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'period_id');
    }

    public function parentPeriod()
    {
        return $this->belongsTo(LessonPeriod::class, 'parent_id');
    }

    public function childPeriods()
    {
        return $this->hasMany(LessonPeriod::class, 'parent_id');
    }

    public function getWeekdayNameAttribute()
    {
        return self::WEEKDAYS[$this->weekday] ?? 'Unknown';
    }
    
    public function overlapsWithPeriod(LessonPeriod $otherPeriod): bool
    {
        if ($this->weekday != $otherPeriod->weekday || $this->semester_id != $otherPeriod->semester_id) {
            return false;
        }

        $thisStart = strtotime($this->time_begin);
        $thisEnd = strtotime($this->time_end);
        $otherStart = strtotime($otherPeriod->time_begin);
        $otherEnd = strtotime($otherPeriod->time_end);

        return !($thisEnd <= $otherStart || $thisStart >= $otherEnd);
    }
}