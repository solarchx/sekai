<?php

namespace App\Exports\Sheets;

use App\Models\StudentScore;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StudentScoreSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return StudentScore::all(['activity_id', 'student_id', 'score_distribution_id', 'score']);
    }

    public function headings(): array
    {
        return ['activity_id', 'student_id', 'score_distribution_id', 'score'];
    }

    public function title(): string
    {
        return 'student_scores';
    }
}