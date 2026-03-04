<?php

namespace App\Exports\Sheets;

use App\Models\ScoreDistribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ScoreDistributionSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return ScoreDistribution::all(['id', 'activity_id', 'name', 'weight']);
    }

    public function headings(): array
    {
        return ['id', 'activity_id', 'name', 'weight'];
    }

    public function title(): string
    {
        return 'score_distributions';
    }
}