<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LessonPeriodSheetTemplate implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [];
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