<?php

namespace App\Imports\Sheets;

use App\Models\Announcement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AnnouncementImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $ann = new Announcement();
        $ann->id = $row['id'];
        $ann->title = $row['title'];
        $ann->subtitle = $row['subtitle'];
        $ann->content = $row['content'];
        $ann->sender_id = $row['sender_id'];
        $ann->scope = $row['scope'];
        $ann->activity_id = $row['activity_id'];
        $ann->grade_id = $row['grade_id'];
        $ann->save();
        return $ann;
    }
}