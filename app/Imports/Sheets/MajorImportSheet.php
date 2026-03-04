<?php

namespace App\Imports\Sheets;

use App\Models\Major;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MajorImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $major = new Major();
        $major->id = $row['id'];
        $major->name = $row['name'];
        $major->save();
        return $major;
    }
}