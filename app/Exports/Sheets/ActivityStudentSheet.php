<?php

namespace App\Exports\Sheets;

use App\Models\ActivityStudent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ActivityStudentSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return ActivityStudent::all(['student_id', 'activity_id']);
    }

    public function headings(): array
    {
        return ['student_id', 'activity_id'];
    }

    public function title(): string
    {
        return 'activity_students';
    }
}