<?php

namespace App\Exports\Sheets;

use App\Models\ActivityForm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ActivityFormSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return ActivityForm::all(['id', 'activity_id', 'activity_date']);
    }

    public function headings(): array
    {
        return ['id', 'activity_id', 'activity_date'];
    }

    public function title(): string
    {
        return 'activity_forms';
    }
}