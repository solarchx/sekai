<?php

namespace App\Imports\Sheets;

use App\Models\AcademicSemester;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AcademicSemesterImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $semester = new AcademicSemester();
        $semester->id = $row['id'];
        $semester->academic_year = $row['academic_year'];
        $semester->semester = $row['semester'];
        $semester->save();
        return $semester;
    }
}