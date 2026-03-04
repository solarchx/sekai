<?php

namespace App\Exports\Sheets;

use App\Models\ActivityReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ActivityReportSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return ActivityReport::all(['presence_id', 'score', 'topic', 'details']);
    }

    public function headings(): array
    {
        return ['presence_id', 'score', 'topic', 'details'];
    }

    public function title(): string
    {
        return 'activity_reports';
    }
}