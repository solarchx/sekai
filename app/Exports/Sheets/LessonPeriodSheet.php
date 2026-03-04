<?php

namespace App\Exports\Sheets;

use App\Models\LessonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LessonPeriodSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return LessonPeriod::all(['id', 'weekday', 'time_begin', 'time_end', 'semester_id', 'major_id', 'grade_id', 'parent_id']);
    }

    public function headings(): array
    {
        return ['id', 'weekday', 'time_begin', 'time_end', 'semester_id', 'major_id', 'grade_id', 'parent_id'];
    }

    public function title(): string
    {
        return 'lesson_periods';
    }
}