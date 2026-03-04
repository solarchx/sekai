<?php

namespace App\Exports\Sheets;

use App\Models\SubjectAvailability;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SubjectAvailabilitySheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return SubjectAvailability::all(['major_id', 'subject_id', 'grade_id']);
    }

    public function headings(): array
    {
        return ['major_id', 'subject_id', 'grade_id'];
    }

    public function title(): string
    {
        return 'subject_availabilities';
    }
}