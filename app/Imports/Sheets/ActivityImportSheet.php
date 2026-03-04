<?php

namespace App\Imports\Sheets;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivityImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $activity = new Activity();
        $activity->id = $row['id'];
        $activity->subject_id = $row['subject_id'];
        $activity->teacher_id = $row['teacher_id'];
        $activity->period_id = $row['period_id'];
        $activity->class_id = $row['class_id'];
        $activity->save();
        return $activity;
    }
}