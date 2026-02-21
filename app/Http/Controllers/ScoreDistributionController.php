<?php

namespace App\Http\Controllers;

use App\Models\ScoreDistribution;
use App\Models\Activity;
use Illuminate\Http\Request;

class ScoreDistributionController extends Controller
{
    public function index()
    {
        $distributions = ScoreDistribution::with('activity.subject', 'activity.teacher', 'activity.class')->get();
        return view('score-distributions.index', compact('distributions'));
    }

    public function create()
    {
        $activities = Activity::with('subject', 'teacher', 'class')->get();
        return view('score-distributions.create', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:1',
        ]);

        // Check for duplicate
        $exists = ScoreDistribution::where('activity_id', $validated['activity_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A score distribution with this name already exists for this activity.']);
        }

        ScoreDistribution::create($validated);

        return redirect()->route('score-distributions.index')->with('success', 'Score distribution created successfully.');
    }

    public function edit(ScoreDistribution $scoreDistribution)
    {
        $activities = Activity::with('subject', 'teacher', 'class')->get();
        return view('score-distributions.edit', compact('scoreDistribution', 'activities'));
    }

    public function update(Request $request, ScoreDistribution $scoreDistribution)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:1',
        ]);

        $scoreDistribution->update($validated);

        return redirect()->route('score-distributions.index')->with('success', 'Score distribution updated successfully.');
    }

    public function destroy(ScoreDistribution $scoreDistribution)
    {
        $scoreDistribution->delete();

        return redirect()->route('score-distributions.index')->with('success', 'Score distribution deleted successfully.');
    }
}
