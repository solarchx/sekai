<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AnnouncementSheetTemplate implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return ['id', 'title', 'subtitle', 'content', 'sender_id', 'scope', 'activity_id', 'grade_id'];
    }

    public function title(): string
    {
        return 'announcements';
    }
}