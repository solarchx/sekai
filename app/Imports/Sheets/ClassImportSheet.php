<?php

namespace App\Imports\Sheets;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $class = new SchoolClass();
        $class->id = $row['id'];
        $class->name = $row['name'];
        $class->major_id = $row['major_id'];
        $class->grade_id = $row['grade_id'];
        $class->capacity = $row['capacity'];
        $class->homeroom_teacher_id = $row['homeroom_teacher_id'];
        $class->save();
        return $class;
    }
}