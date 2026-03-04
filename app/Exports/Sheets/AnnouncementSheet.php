<?php

namespace App\Exports\Sheets;

use App\Models\Announcement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AnnouncementSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return Announcement::all(['id', 'title', 'subtitle', 'content', 'sender_id', 'scope', 'activity_id', 'grade_id']);
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