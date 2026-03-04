<?php

namespace App\Imports\Sheets;

use App\Models\Grade;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradeImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $grade = new Grade();
        $grade->id = $row['id'];
        $grade->save();
        return $grade;
    }
}