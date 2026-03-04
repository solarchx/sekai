<?php

namespace App\Exports\Sheets;

use App\Models\AcademicSemester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AcademicSemesterSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return AcademicSemester::all(['id', 'academic_year', 'semester']);
    }

    public function headings(): array
    {
        return ['id', 'academic_year', 'semester'];
    }

    public function title(): string
    {
        return 'academic_semesters';
    }
}