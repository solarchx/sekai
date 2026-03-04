<?php

namespace App\Imports\Sheets;

use App\Models\ActivityPresence;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ActivityPresenceImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $ap = new ActivityPresence();
        $ap->id = $row['id'];
        $ap->form_id = $row['form_id'];
        $ap->student_id = $row['student_id'];
        $ap->score = $row['score'];
        $ap->location = $row['location'];
        $ap->save();
        return $ap;
    }
}