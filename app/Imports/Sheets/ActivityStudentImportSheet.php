<?php

namespace App\Imports\Sheets;

use App\Models\ActivityStudent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivityStudentImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $as = new ActivityStudent();
        $as->student_id = $row['student_id'];
        $as->activity_id = $row['activity_id'];
        $as->save();
        return $as;
    }
}