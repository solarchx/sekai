<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\AcademicSemester;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\SubjectAvailability;
use App\Models\LessonPeriod;
use App\Models\Activity;
use App\Models\ActivityStudent;
use App\Models\ScoreDistribution;
use App\Models\ActivityForm;
use App\Models\ActivityPresence;
use App\Models\ActivityReport;
use App\Models\StudentScore;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatabaseExport;
use App\Imports\DatabaseImport;
use App\Imports\DatabaseTemplate;

class DataTransferController extends Controller
{
    /**
     * Download a template Excel file with all table structures.
     */
    public function downloadTemplate()
    {
        return Excel::download(new DatabaseTemplate, 'database_template.xlsx');
    }

    /**
     * Import data from an uploaded Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:40960',
        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            return back()->withErrors('File upload failed: ' . $file->getErrorMessage());
        }

        try {
            DB::beginTransaction();

            Excel::import(new DatabaseImport, $file);

            DB::commit();

            return redirect()->back()->with('success', 'Data imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()->withErrors('Import validation failed: ' . implode('; ', $errorMessages));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export all database data to an Excel file.
     */
    public function export()
    {
        return Excel::download(new DatabaseExport, 'database_export_' . date('Y-m-d_His') . '.xlsx');
    }
}