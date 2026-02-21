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
    ];

    const WEEKDAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function semester()
    {
        return $this->belongsTo(AcademicSemester::class, 'semester_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'period_id');
    }

    public function getWeekdayNameAttribute()
    {
        return self::WEEKDAYS[$this->weekday] ?? 'Unknown';
    }

    /**
     * Check if this period overlaps with another period
     */
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