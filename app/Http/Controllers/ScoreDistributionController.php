<?php

namespace App\Http\Controllers;

use App\Models\ScoreDistribution;
use App\Models\Activity;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoreDistributionController extends Controller
{
    public function index(Activity $activity, Request $request)
    {
        try {
            $distributions = ScoreDistribution::where('activity_id', $activity->id)->paginate(100);

            return view('score-distributions.index', compact('activity', 'distributions'));
        } catch (\Exception $e) {
            Log::error('Error loading distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading distributions: ' . $e->getMessage());
        }
    }

    public function create(Activity $activity)
    {
        return view('score-distributions.create', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $distributions = ScoreDistribution::where('activity_id', $activity->id)->get();
        return view('score-distributions.create', compact('activity', 'distributions'));
    }

    public function store(Request $request, Activity $activity)
    {
        try {
            $validated = $request->validate([
                'distributions' => 'required|array|min:1',
                'distributions.*.name' => 'required|string|max:255',
                'distributions.*.weight' => 'required|integer|min:1|max:100',
            ]);

            $totalWeight = array_sum(array_column($validated['distributions'], 'weight'));
            if ($totalWeight !== 100) {
                return back()->withErrors(['distributions' => "Total weight must equal 100. Current total: {$totalWeight}"])->withInput();
            }

            DB::beginTransaction();

            ScoreDistribution::where('activity_id', $activity->id)->delete();

            foreach ($validated['distributions'] as $dist) {
                ScoreDistribution::create([
                    'activity_id' => $activity->id,
                    'name' => $dist['name'],
                    'weight' => $dist['weight'],
                ]);
            }

            DB::commit();

            $this->syncStudentScores($activity);

            return redirect()->route('score-distributions.index', $activity)
                ->with('success', 'Score distributions saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error saving distributions: ' . $e->getMessage());
        }
    }

    private function syncStudentScores(Activity $activity)
    {
        $distributionIds = ScoreDistribution::where('activity_id', $activity->id)->pluck('id');
        $studentIds = $activity->students()->pluck('users.id');

        $existingScores = StudentScore::where('activity_id', $activity->id)
            ->get()
            ->groupBy('student_id')
            ->map(fn($items) => $items->keyBy('score_distribution_id'));

        foreach ($studentIds as $studentId) {
            foreach ($distributionIds as $distId) {
                if (!isset($existingScores[$studentId][$distId])) {
                    StudentScore::create([
                        'activity_id' => $activity->id,
                        'student_id' => $studentId,
                        'score_distribution_id' => $distId,
                        'score' => 0,
                    ]);
                }
            }
        }

        StudentScore::where('activity_id', $activity->id)
            ->whereNotIn('score_distribution_id', $distributionIds)
            ->forceDelete();
    }
}