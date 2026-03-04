<?php

namespace App\Imports\Sheets;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $subject = new Subject();
        $subject->id = $row['id'];
        $subject->name = $row['name'];
        $subject->save();
        return $subject;
    }
}