<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\ActivityPresence;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    public function index()
    {
        $reports = ActivityReport::with('presence.student', 'presence.form.activity.subject')->get();
        return view('activity-reports.index', compact('reports'));
    }

    public function create()
    {
        $presences = ActivityPresence::with('student', 'form.activity.subject')->get();
        return view('activity-reports.create', compact('presences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'presence_id' => 'required|exists:activity_presences,id',
            'score' => 'required|integer|between:0,100',
            'topic' => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        ActivityReport::create($validated);

        return redirect()->route('activity-reports.index')->with('success', 'Activity report created successfully.');
    }

    public function edit(ActivityReport $activityReport)
    {
        $presences = ActivityPresence::with('student', 'form.activity.subject')->get();
        return view('activity-reports.edit', compact('activityReport', 'presences'));
    }

    public function update(Request $request, ActivityReport $activityReport)
    {
        $validated = $request->validate([
            'presence_id' => 'required|exists:activity_presences,id',
            'score' => 'required|integer|between:0,100',
            'topic' => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        $activityReport->update($validated);

        return redirect()->route('activity-reports.index')->with('success', 'Activity report updated successfully.');
    }

    public function destroy(ActivityReport $activityReport)
    {
        $activityReport->delete();

        return redirect()->route('activity-reports.index')->with('success', 'Activity report deleted successfully.');
    }
}
