<?php

namespace App\Http\Controllers;

use App\Models\ActivityReport;
use App\Models\ActivityPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityReportController extends Controller
{
    /**
     * Display all activity reports (admin only).
     * Students should not see each other's reports due to anonymity.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403);
        }
        
        $query = ActivityReport::with('presence.form.activity.subject');
        
        $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
        if ($showDeleted) {
            $query = $query->onlyTrashed();
        }
        
        $reports = $query->paginate(100);
        return view('activity-reports.index', compact('reports', 'showDeleted'));
    }

    /**
     * Show the form for creating an anonymous report.
     */
    public function create()
    {
        try {
            // Students can only report on their own presences
            $presences = ActivityPresence::where('student_id', auth()->id())
                ->with('student', 'form.activity.subject', 'form.activity.teacher')
                ->where('deleted_at', null)
                ->get();
            
            if ($presences->isEmpty()) {
                return redirect()->route('dashboard')->withErrors('You have no activity presences to report on.');
            }
            
            return view('activity-reports.create', compact('presences'));
        } catch (\Exception $e) {
            Log::error('Error loading report create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store an anonymous activity report.
     * This is for students to rate teachers.
     */
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

    /**
     * Edit is not allowed for anonymous reports.
     */
    public function edit(ActivityReport $activityReport)
    {
        if ($activityReport->presence->student_id !== auth()->id()) {
            abort(403);
        }
        return view('activity-reports.edit', compact('activityReport'));
    }

    /**
     * Update is not allowed for anonymous reports.
     */
    public function update(Request $request, ActivityReport $activityReport)
    {
        if ($activityReport->presence->student_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'score'   => 'required|integer|between:0,3',
            'topic'   => 'required|string|max:255',
            'details' => 'required|string',
        ]);

        $activityReport->update($validated);
        return redirect()->route('dashboard')->with('success', 'Report updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityReport $activityReport)
    {
        // Student can delete own, admin can delete any
        if ($activityReport->presence->student_id !== auth()->id() && auth()->user()->role !== 'ADMIN') {
            abort(403);
        }
        $activityReport->delete();
        return redirect()->back()->with('success', 'Report deleted.');
    }

    /**
     * Restore a soft-deleted report (admin only).
     */
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
