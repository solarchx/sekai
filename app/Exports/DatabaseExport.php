<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\MajorSheet;
use App\Exports\Sheets\GradeSheet;
use App\Exports\Sheets\SubjectSheet;
use App\Exports\Sheets\AcademicSemesterSheet;
use App\Exports\Sheets\ClassSheet;
use App\Exports\Sheets\UserSheet;
use App\Exports\Sheets\SubjectAvailabilitySheet;
use App\Exports\Sheets\LessonPeriodSheet;
use App\Exports\Sheets\ActivitySheet;
use App\Exports\Sheets\ActivityStudentSheet;
use App\Exports\Sheets\ScoreDistributionSheet;
use App\Exports\Sheets\ActivityFormSheet;
use App\Exports\Sheets\ActivityPresenceSheet;
use App\Exports\Sheets\ActivityReportSheet;
use App\Exports\Sheets\StudentScoreSheet;
use App\Exports\Sheets\AnnouncementSheet;

class DatabaseExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MajorSheet(),
            new GradeSheet(),
            new SubjectSheet(),
            new AcademicSemesterSheet(),
            new ClassSheet(),
            new UserSheet(),
            new SubjectAvailabilitySheet(),
            new LessonPeriodSheet(),
            new ActivitySheet(),
            new ActivityStudentSheet(),
            new ScoreDistributionSheet(),
            new ActivityFormSheet(),
            new ActivityPresenceSheet(),
            new ActivityReportSheet(),
            new StudentScoreSheet(),
            new AnnouncementSheet(),
        ];
    }
}