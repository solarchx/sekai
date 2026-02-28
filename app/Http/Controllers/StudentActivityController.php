<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityForm;
use App\Models\AcademicSemester;
use App\Models\ScoreDistribution;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentActivityController extends Controller
{
    /**
     * Display a list of activities the student is enrolled in,
     * and the forms for a selected activity.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'STUDENT') {
                abort(403, 'Unauthorized access.');
            }

            $activities = $user->activitiesAsStudent()
                ->with(['subject', 'teacher', 'class', 'period.semester'])
                ->get();

            $selectedActivity = null;
            $forms = collect();

            $activityId = $request->query('activity_id');
            if ($activityId) {
                $selectedActivity = $activities->firstWhere('id', $activityId);
                if (!$selectedActivity) {
                    return redirect()->route('student.activities')
                        ->withErrors('Invalid activity selected.');
                }

                $forms = $selectedActivity->forms()
                    ->with(['presences' => function ($q) use ($user) {
                        $q->where('student_id', $user->id)->with('report');
                    }])
                    ->orderBy('activity_date', 'desc')
                    ->get();
            }

            return view('student.activities', compact('activities', 'selectedActivity', 'forms'));
        } catch (\Exception $e) {
            Log::error('Error loading student activities: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Error loading activities.');
        }
    }

    /**
     * Display the student's grades, grouped by semester.
     */
    public function grades(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'STUDENT') {
                abort(403, 'Unauthorized access.');
            }

            $semesters = AcademicSemester::orderBy('academic_year', 'desc')
                ->orderBy('semester', 'desc')
                ->get();

            $selectedSemesterId = $request->query('semester_id');
            $activities = collect();

            if ($selectedSemesterId) {
                $activities = $user->activitiesAsStudent()
                    ->whereHas('period', function ($q) use ($selectedSemesterId) {
                        $q->where('semester_id', $selectedSemesterId);
                    })
                    ->with(['subject', 'teacher', 'period.semester', 'scoreDistributions'])
                    ->get();

                foreach ($activities as $activity) {
                    $totalWeight = $activity->scoreDistributions->sum('weight');
                    $studentScores = StudentScore::where('activity_id', $activity->id)
                        ->where('student_id', $user->id)
                        ->get()
                        ->keyBy('name');

                    $breakdown = [];
                    $weightedTotal = 0;

                    foreach ($activity->scoreDistributions as $dist) {
                        $score = $studentScores->get($dist->name);
                        $scoreValue = $score ? $score->score : 0;
                        $weightPercent = $totalWeight > 0 ? $dist->weight / $totalWeight : 0;
                        $contribution = $scoreValue * $weightPercent;
                        $weightedTotal += $contribution;

                        $breakdown[] = [
                            'name' => $dist->name,
                            'weight' => $dist->weight,
                            'weight_percent' => $weightPercent * 100,
                            'score' => $scoreValue,
                            'contribution' => $contribution,
                        ];
                    }

                    $activity->breakdown = $breakdown;
                    $activity->weighted_total = round($weightedTotal, 2);
                }
            }

            return view('student.grades', compact('semesters', 'selectedSemesterId', 'activities'));
        } catch (\Exception $e) {
            Log::error('Error loading student grades: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Error loading grades.');
        }
    }
}