<?php

namespace App\Http\Controllers;

use App\Models\ScoreDistribution;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoreDistributionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = ScoreDistribution::with('activity.subject', 'activity.teacher', 'activity.class');
            
            if ($showDeleted) {
                $distributions = $query->onlyTrashed()->paginate(100);
            } else {
                $distributions = $query->paginate(100);
            }
            
            return view('score-distributions.index', compact('distributions', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading distributions: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $activities = Activity::with('subject', 'teacher', 'class')
                ->where('deleted_at', null)
                ->get();
            
            if ($activities->isEmpty()) {
                return redirect()->route('score-distributions.index')->withErrors('No activities available.');
            }
            
            return view('score-distributions.create', compact('activities'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'distributions' => 'required|array|min:1',
                'distributions.*.name' => 'required|string|max:255',
                'distributions.*.weight' => 'required|integer|min:1|max:100',
            ], [
                'activity_id.required' => 'Activity is required.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'distributions.required' => 'At least one score distribution is required.',
                'distributions.min' => 'At least one score distribution is required.',
                'distributions.*.name.required' => 'Score distribution name is required.',
                'distributions.*.name.max' => 'Score distribution name must not exceed 255 characters.',
                'distributions.*.weight.required' => 'Score distribution weight is required.',
                'distributions.*.weight.integer' => 'Score distribution weight must be a number.',
                'distributions.*.weight.min' => 'Score distribution weight must be at least 1.',
                'distributions.*.weight.max' => 'Score distribution weight must not exceed 100.',
            ]);

            $activity = Activity::find($validated['activity_id']);

            
            $totalWeight = array_sum(array_column($validated['distributions'], 'weight'));
            if ($totalWeight !== 100) {
                return back()->withErrors(['distributions' => "Total weight must equal 100. Current total: {$totalWeight}"])->withInput();
            }

            DB::beginTransaction();

            
            ScoreDistribution::where('activity_id', $validated['activity_id'])->delete();

            
            foreach ($validated['distributions'] as $index => $distribution) {
                
                $exists = ScoreDistribution::where('activity_id', $validated['activity_id'])
                    ->where('name', $distribution['name'])
                    ->where('deleted_at', null)
                    ->exists();

                if ($exists) {
                    DB::rollBack();
                    return back()->withErrors(['distributions' => "Duplicate distribution name '{$distribution['name']}' within activity."])->withInput();
                }

                ScoreDistribution::create([
                    'activity_id' => $validated['activity_id'],
                    'name' => $distribution['name'],
                    'weight' => $distribution['weight'],
                ]);
            }

            DB::commit();

            return redirect()->route('score-distributions.index')->with('success', 'Score distributions created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating distributions: ' . $e->getMessage());
        }
    }

    public function edit(ScoreDistribution $scoreDistribution)
    {
        try {
            $activities = Activity::with('subject', 'teacher', 'class')
                ->where('deleted_at', null)
                ->get();
            
            
            $activityDistributions = ScoreDistribution::where('activity_id', $scoreDistribution->activity_id)
                ->where('deleted_at', null)
                ->get();
            
            return view('score-distributions.edit', compact('scoreDistribution', 'activities', 'activityDistributions'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ScoreDistribution $scoreDistribution)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'distributions' => 'required|array|min:1',
                'distributions.*.name' => 'required|string|max:255',
                'distributions.*.weight' => 'required|integer|min:1|max:100',
            ], [
                'activity_id.required' => 'Activity is required.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'distributions.required' => 'At least one score distribution is required.',
                'distributions.min' => 'At least one score distribution is required.',
                'distributions.*.name.required' => 'Score distribution name is required.',
                'distributions.*.name.max' => 'Score distribution name must not exceed 255 characters.',
                'distributions.*.weight.required' => 'Score distribution weight is required.',
                'distributions.*.weight.integer' => 'Score distribution weight must be a number.',
                'distributions.*.weight.min' => 'Score distribution weight must be at least 1.',
                'distributions.*.weight.max' => 'Score distribution weight must not exceed 100.',
            ]);

            
            $totalWeight = array_sum(array_column($validated['distributions'], 'weight'));
            if ($totalWeight !== 100) {
                return back()->withErrors(['distributions' => "Total weight must equal 100. Current total: {$totalWeight}"])->withInput();
            }

            DB::beginTransaction();

            
            ScoreDistribution::where('activity_id', $validated['activity_id'])->delete();

            
            foreach ($validated['distributions'] as $distribution) {
                ScoreDistribution::create([
                    'activity_id' => $validated['activity_id'],
                    'name' => $distribution['name'],
                    'weight' => $distribution['weight'],
                ]);
            }

            DB::commit();

            return redirect()->route('score-distributions.index')->with('success', 'Score distributions updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating distributions: ' . $e->getMessage());
        }
    }

    public function destroy(ScoreDistribution $scoreDistribution)
    {
        try {
            DB::beginTransaction();

            $activityId = $scoreDistribution->activity_id;

            
            ScoreDistribution::where('activity_id', $activityId)->delete();

            DB::commit();

            return redirect()->route('score-distributions.index')->with('success', 'Score distributions deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting distributions: ' . $e->getMessage());
        }
    }

    
    public function restore(ScoreDistribution $scoreDistribution)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            
            ScoreDistribution::where('activity_id', $scoreDistribution->activity_id)
                ->onlyTrashed()
                ->restore();

            return redirect()->route('score-distributions.index')->with('success', 'Score distributions restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring distributions: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring distributions: ' . $e->getMessage());
        }
    }
}
