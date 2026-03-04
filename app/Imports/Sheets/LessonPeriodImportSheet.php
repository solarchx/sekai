<?php

namespace App\Imports\Sheets;

use App\Models\LessonPeriod;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LessonPeriodImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $lp = new LessonPeriod();
        $lp->id = $row['id'];
        $lp->weekday = $row['weekday'];
        $lp->time_begin = $row['time_begin'];
        $lp->time_end = $row['time_end'];
        $lp->semester_id = $row['semester_id'];
        $lp->major_id = $row['major_id'];
        $lp->grade_id = $row['grade_id'];
        $lp->parent_id = $row['parent_id'];
        $lp->save();
        return $lp;
    }
}