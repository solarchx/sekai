<?php

namespace App\Imports\Sheets;

use App\Models\SubjectAvailability;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectAvailabilityImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $sa = new SubjectAvailability();
        $sa->major_id = $row['major_id'];
        $sa->subject_id = $row['subject_id'];
        $sa->grade_id = $row['grade_id'];
        $sa->save();
        return $sa;
    }
}