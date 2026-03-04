<?php

namespace App\Exports\Sheets;

use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GradeSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Grade::all(['id']);
    }

    public function headings(): array
    {
        return ['id'];
    }

    public function title(): string
    {
        return 'grades';
    }
}