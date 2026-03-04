<?php

namespace App\Imports\Sheets;

use App\Models\ScoreDistribution;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScoreDistributionImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $sd = new ScoreDistribution();
        $sd->id = $row['id'];
        $sd->activity_id = $row['activity_id'];
        $sd->name = $row['name'];
        $sd->weight = $row['weight'];
        $sd->save();
        return $sd;
    }
}