<?php

namespace App\Imports\Sheets;

use App\Models\StudentScore;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentScoreImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $ss = new StudentScore();
        $ss->activity_id = $row['activity_id'];
        $ss->student_id = $row['student_id'];
        $ss->score_distribution_id = $row['score_distribution_id'];
        $ss->score = $row['score'];
        $ss->save();
        return $ss;
    }
}