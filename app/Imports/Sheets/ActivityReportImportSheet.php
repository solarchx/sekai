<?php

namespace App\Imports\Sheets;

use App\Models\ActivityReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivityReportImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $ar = new ActivityReport();
        $ar->presence_id = $row['presence_id'];
        $ar->score = $row['score'];
        $ar->topic = $row['topic'];
        $ar->details = $row['details'];
        $ar->save();
        return $ar;
    }
}