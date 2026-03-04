<?php

namespace App\Exports\Sheets;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubjectSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Subject::all(['id', 'name']);
    }

    public function headings(): array
    {
        return ['id', 'name'];
    }

    public function title(): string
    {
        return 'subjects';
    }
}