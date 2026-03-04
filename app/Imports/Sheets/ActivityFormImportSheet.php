<?php

namespace App\Imports\Sheets;

use App\Models\ActivityForm;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivityFormImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $af = new ActivityForm();
        $af->id = $row['id'];
        $af->activity_id = $row['activity_id'];
        $af->activity_date = $row['activity_date'];
        $af->save();
        return $af;
    }
}