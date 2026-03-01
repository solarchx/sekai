<?php

namespace App\Http\Controllers;

use App\Models\StudentScore;
use App\Models\Activity;
use App\Models\User;
use App\Models\ScoreDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentScoreController extends Controller
{
    public function index(Activity $activity)
    {
        try {
            $students = $activity->class->students()
                ->where('role', 'STUDENT')
                ->where('deleted_at', null)
                ->orderBy('student_order')
                ->get(['id', 'name', 'identifier']);

            $distributions = ScoreDistribution::where('activity_id', $activity->id)
                ->where('deleted_at', null)
                ->get();

            $scores = StudentScore::where('activity_id', $activity->id)
                ->get()
                ->groupBy('student_id')
                ->map(fn($items) => $items->keyBy('score_distribution_id'));

            return view('student-scores.index', compact('activity', 'students', 'distributions', 'scores'));
        } catch (\Exception $e) {
            Log::error('Error loading student scores: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading student scores: ' . $e->getMessage());
        }
    }

    public function edit(Activity $activity, User $student)
    {
        try {
            $isEnrolled = $activity->students()->where('users.id', $student->id)->exists();
            if (!$isEnrolled) {
                return redirect()->route('student-scores.index', $activity)
                    ->withErrors('Student is not enrolled in this activity.');
            }

            $distributions = ScoreDistribution::where('activity_id', $activity->id)
                ->where('deleted_at', null)
                ->get();

            $existingScores = StudentScore::where('activity_id', $activity->id)
                ->where('student_id', $student->id)
                ->get()
                ->keyBy('score_distribution_id');

            return view('student-scores.edit', compact('activity', 'student', 'distributions', 'existingScores'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Activity $activity, User $student)
    {
        try {
            $isEnrolled = $activity->students()->where('users.id', $student->id)->exists();
            if (!$isEnrolled) {
                return redirect()->route('student-scores.index', $activity)
                    ->withErrors('Student is not enrolled in this activity.');
            }

            $validated = $request->validate([
                'scores' => 'required|array',
                'scores.*' => 'required|integer|between:0,100',
            ]);

            $distributionIds = ScoreDistribution::where('activity_id', $activity->id)
                ->pluck('id')
                ->toArray();

            $submittedIds = array_keys($validated['scores']);
            $invalid = array_diff($submittedIds, $distributionIds);
            if (!empty($invalid)) {
                return back()->withErrors(['scores' => 'Invalid distribution IDs.'])->withInput();
            }

            DB::beginTransaction();

            StudentScore::where('activity_id', $activity->id)
                ->where('student_id', $student->id)
                ->forceDelete();

            foreach ($validated['scores'] as $distId => $score) {
                StudentScore::create([
                    'activity_id' => $activity->id,
                    'student_id' => $student->id,
                    'score_distribution_id' => $distId,
                    'score' => $score,
                ]);
            }

            DB::commit();

            return redirect()->route('student-scores.index', $activity)
                ->with('success', "Scores for {$student->name} updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating student scores: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating student scores: ' . $e->getMessage());
        }
    }
}