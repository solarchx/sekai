<?php

namespace App\Exports\Sheets;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return User::all(['id', 'name', 'email', 'identifier', 'role', 'class_id', 'student_order']);
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