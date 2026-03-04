<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\Sheets\MajorImportSheet;
use App\Imports\Sheets\GradeImportSheet;
use App\Imports\Sheets\SubjectImportSheet;
use App\Imports\Sheets\AcademicSemesterImportSheet;
use App\Imports\Sheets\ClassImportSheet;
use App\Imports\Sheets\UserImportSheet;
use App\Imports\Sheets\SubjectAvailabilityImportSheet;
use App\Imports\Sheets\LessonPeriodImportSheet;
use App\Imports\Sheets\ActivityImportSheet;
use App\Imports\Sheets\ActivityStudentImportSheet;
use App\Imports\Sheets\ScoreDistributionImportSheet;
use App\Imports\Sheets\ActivityFormImportSheet;
use App\Imports\Sheets\ActivityPresenceImportSheet;
use App\Imports\Sheets\ActivityReportImportSheet;
use App\Imports\Sheets\StudentScoreImportSheet;
use App\Imports\Sheets\AnnouncementImportSheet;

class DatabaseImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MajorImportSheet(),
            new GradeImportSheet(),
            new SubjectImportSheet(),
            new AcademicSemesterImportSheet(),
            new ClassImportSheet(),
            new UserImportSheet(),
            new SubjectAvailabilityImportSheet(),
            new LessonPeriodImportSheet(),
            new ActivityImportSheet(),
            new ActivityStudentImportSheet(),
            new ScoreDistributionImportSheet(),
            new ActivityFormImportSheet(),
            new ActivityPresenceImportSheet(),
            new ActivityReportImportSheet(),
            new StudentScoreImportSheet(),
            new AnnouncementImportSheet(),
        ];
    }
}