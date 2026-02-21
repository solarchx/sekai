<?php

namespace App\Http\Controllers;

use App\Models\ActivityPresence;
use App\Models\ActivityForm;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityPresenceController extends Controller
{
    public function index()
    {
        $presences = ActivityPresence::with('form.activity.subject', 'student')->get();
        return view('activity-presences.index', compact('presences'));
    }

    public function create()
    {
        $forms = ActivityForm::with('activity.subject', 'activity.teacher', 'activity.class', 'activity.students')->get();
        return view('activity-presences.create', compact('forms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:activity_forms,id',
            'student_id' => 'required|exists:users,id',
            'score' => 'required|integer|between:0,100',
        ]);

        // Check for duplicate
        $exists = ActivityPresence::where('form_id', $validated['form_id'])
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['student_id' => 'This student already has a presence record for this form.']);
        }

        ActivityPresence::create($validated);

        return redirect()->route('activity-presences.index')->with('success', 'Presence record created successfully.');
    }

    public function edit(ActivityPresence $activityPresence)
    {
        $forms = ActivityForm::with('activity.subject', 'activity.teacher', 'activity.class', 'activity.students')->get();
        return view('activity-presences.edit', compact('activityPresence', 'forms'));
    }

    public function update(Request $request, ActivityPresence $activityPresence)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:activity_forms,id',
            'student_id' => 'required|exists:users,id',
            'score' => 'required|integer|between:0,100',
        ]);

        $activityPresence->update($validated);

        return redirect()->route('activity-presences.index')->with('success', 'Presence record updated successfully.');
    }

    public function destroy(ActivityPresence $activityPresence)
    {
        $activityPresence->delete();

        return redirect()->route('activity-presences.index')->with('success', 'Presence record deleted successfully.');
    }
}
