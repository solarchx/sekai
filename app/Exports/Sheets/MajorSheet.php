<?php

namespace App\Exports\Sheets;

use App\Models\Major;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MajorSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Major::all(['id', 'name']);
    }

    public function headings(): array
    {
        return ['id', 'name'];
    }

    public function title(): string
    {
        return 'majors';
    }
}