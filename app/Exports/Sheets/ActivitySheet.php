<?php

namespace App\Exports\Sheets;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ActivitySheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Activity::all(['id', 'subject_id', 'teacher_id', 'period_id', 'class_id']);
    }

    public function headings(): array
    {
        return ['id', 'subject_id', 'teacher_id', 'period_id', 'class_id'];
    }

    public function title(): string
    {
        return 'activities';
    }
}