<?php

namespace App\Exports\Sheets;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClassSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return SchoolClass::all(['id', 'name', 'major_id', 'grade_id', 'capacity', 'homeroom_teacher_id']);
    }

    public function headings(): array
    {
        return ['id', 'name', 'major_id', 'grade_id', 'capacity', 'homeroom_teacher_id'];
    }

    public function title(): string
    {
        return 'classes';
    }
}