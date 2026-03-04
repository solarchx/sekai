<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\MajorSheetTemplate;
use App\Exports\Sheets\GradeSheetTemplate;
use App\Exports\Sheets\SubjectSheetTemplate;
use App\Exports\Sheets\AcademicSemesterSheetTemplate;
use App\Exports\Sheets\ClassSheetTemplate;
use App\Exports\Sheets\UserSheetTemplate;
use App\Exports\Sheets\SubjectAvailabilitySheetTemplate;
use App\Exports\Sheets\LessonPeriodSheetTemplate;
use App\Exports\Sheets\ActivitySheetTemplate;
use App\Exports\Sheets\ActivityStudentSheetTemplate;
use App\Exports\Sheets\ScoreDistributionSheetTemplate;
use App\Exports\Sheets\ActivityFormSheetTemplate;
use App\Exports\Sheets\ActivityPresenceSheetTemplate;
use App\Exports\Sheets\ActivityReportSheetTemplate;
use App\Exports\Sheets\StudentScoreSheetTemplate;
use App\Exports\Sheets\AnnouncementSheetTemplate;

class DatabaseTemplate implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MajorSheetTemplate(),
            new GradeSheetTemplate(),
            new SubjectSheetTemplate(),
            new AcademicSemesterSheetTemplate(),
            new ClassSheetTemplate(),
            new UserSheetTemplate(),
            new SubjectAvailabilitySheetTemplate(),
            new LessonPeriodSheetTemplate(),
            new ActivitySheetTemplate(),
            new ActivityStudentSheetTemplate(),
            new ScoreDistributionSheetTemplate(),
            new ActivityFormSheetTemplate(),
            new ActivityPresenceSheetTemplate(),
            new ActivityReportSheetTemplate(),
            new StudentScoreSheetTemplate(),
            new AnnouncementSheetTemplate(),
        ];
    }
}