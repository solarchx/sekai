<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\ActivityPresence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityReportController extends Controller
{
    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
            return redirect()->route('wrongway');
        }

        $teacherId = $request->query('teacher_id');
        $teachers = User::where('role', '!=', 'STUDENT')->orderBy('name')->get();

        $query = ActivityReport::with([
            'presence.form.activity.subject',
            'presence.form.activity.teacher',
            'presence.form.activity.class'
        ]);

        if ($teacherId) {
            $query->whereHas('presence.form.activity', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        $showDeleted = $request->has('show_deleted');
        if ($showDeleted) {
            $query = $query->onlyTrashed();
        }

        $reports = $query->paginate(100);

        return view('activity-reports.index', compact('reports', 'teachers', 'teacherId', 'showDeleted'));
    }

    public function create(Request $request)
    {
        try {
            $presenceId = $request->query('presence_id');
            return view('activity-reports.create', compact('presenceId'));
        } catch (\Exception $e) {
            Log::error('Error loading report create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'presence_id' => 'required|exists:activity_presences,id',
            'score'       => 'required|integer|between:0,3',
            'topic'       => 'required|string|max:255',
            'details'     => 'required|string',
        ]);

        $presence = ActivityPresence::find($validated['presence_id']);
        if ($presence->student_id !== auth()->id()) {
            return back()->withErrors('You can only report on your own presences.');
        }

        if (ActivityReport::where('presence_id', $validated['presence_id'])->exists()) {
            return back()->withErrors('You have already submitted a report for this activity.');
        }

        DB::transaction(function () use ($validated) {
            ActivityReport::create($validated);
        });

        return redirect()->route('dashboard')->with('success', 'Report submitted.');
    }

    public function edit(ActivityReport $activityReport)
    {
        if (!in_array(auth()->user()->role, ['VP', 'ADMIN', 'STUDENT'])) {
            return redirect()->route('wrongway');
        }
        return view('activity-reports.edit', compact('activityReport'));
    }

    public function update(Request $request, ActivityReport $activityReport)
    {
        if (!in_array(auth()->user()->role, ['VP', 'ADMIN', 'STUDENT'])) {
            return redirect()->route('wrongway');
        }

        $validated = $request->validate([
            'score'   => 'required|integer|between:0,3',
            'topic'   => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        $activityReport->update($validated);
        return redirect()->route('dashboard')->with('success', 'Report updated.');
    }

    public function destroy(ActivityReport $activityReport)
    {
        if (!in_array(auth()->user()->role, ['VP', 'ADMIN', 'STUDENT'])) {
            return redirect()->route('wrongway');
        }
        $activityReport->delete();
        return redirect()->back()->with('success', 'Report deleted.');
    }

    public function restore(ActivityReport $activityReport)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $activityReport->restore();

            return redirect()->route('activity-reports.index')->with('success', 'Report restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring report: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring report: ' . $e->getMessage());
        }
    }
}