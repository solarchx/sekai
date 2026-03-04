<?php

namespace App\Exports\Sheets;

use App\Models\ActivityPresence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ActivityPresenceSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return ActivityPresence::all(['id', 'form_id', 'student_id', 'score', 'location']);
    }

    public function headings(): array
    {
        return ['id', 'form_id', 'student_id', 'score', 'location'];
    }

    public function title(): string
    {
        return 'activity_presences';
    }
}