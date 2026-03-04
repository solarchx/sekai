<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserSheetTemplate implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return ['id', 'name', 'email', 'identifier', 'role', 'class_id', 'student_order'];
    }

    public function title(): string
    {
        return 'users';
    }
}