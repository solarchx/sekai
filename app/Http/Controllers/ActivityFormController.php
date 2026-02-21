<?php

namespace App\Http\Controllers;

use App\Models\ActivityForm;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityFormController extends Controller
{
    public function index()
    {
        $forms = ActivityForm::with('activity.subject', 'activity.teacher', 'activity.class')->get();
        return view('activity-forms.index', compact('forms'));
    }

    public function create()
    {
        $activities = Activity::with('subject', 'teacher', 'class')->get();
        return view('activity-forms.create', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'activity_date' => 'required|date',
        ]);

        // Check for duplicate
        $exists = ActivityForm::where('activity_id', $validated['activity_id'])
            ->where('activity_date', $validated['activity_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['activity_date' => 'A form already exists for this activity on this date.']);
        }

        ActivityForm::create($validated);

        return redirect()->route('activity-forms.index')->with('success', 'Activity form created successfully.');
    }

    public function edit(ActivityForm $activityForm)
    {
        $activities = Activity::with('subject', 'teacher', 'class')->get();
        return view('activity-forms.edit', compact('activityForm', 'activities'));
    }

    public function update(Request $request, ActivityForm $activityForm)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'activity_date' => 'required|date',
        ]);

        $activityForm->update($validated);

        return redirect()->route('activity-forms.index')->with('success', 'Activity form updated successfully.');
    }

    public function destroy(ActivityForm $activityForm)
    {
        $activityForm->delete();

        return redirect()->route('activity-forms.index')->with('success', 'Activity form deleted successfully.');
    }
}
