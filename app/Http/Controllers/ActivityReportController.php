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
        try {
            // Only admins can view all reports
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->route('dashboard')->withErrors('You do not have permission to view all reports.');
            }

            $showDeleted = $request->has('show_deleted');
            
            $query = ActivityReport::with('presence.form.activity.subject', 'presence.student');
            
            if ($showDeleted) {
                $reports = $query->onlyTrashed()->paginate(100);
            } else {
                $reports = $query->paginate(100);
            }
            
            return view('activity-reports.index', compact('reports', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading reports: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading reports: ' . $e->getMessage());
        }
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
        try {
            $validated = $request->validate([
                'presence_id' => 'required|exists:activity_presences,id',
                'teacher_rating' => 'required|integer|between:1,5',
                'feedback' => 'nullable|string|max:500',
            ], [
                'presence_id.required' => 'Activity presence is required.',
                'presence_id.exists' => 'Selected presence does not exist.',
                'teacher_rating.required' => 'Teacher rating is required.',
                'teacher_rating.integer' => 'Teacher rating must be a number.',
                'teacher_rating.between' => 'Teacher rating must be between 1 and 5.',
                'feedback.max' => 'Feedback must not exceed 500 characters.',
            ]);

            $presence = ActivityPresence::with('form.activity', 'student')->find($validated['presence_id']);

            // Verify the presence belongs to the authenticated student
            if ($presence->student_id !== auth()->id()) {
                return back()->withErrors('You can only report on your own presences.')->withInput();
            }

            // Check if a report already exists for this presence
            $exists = ActivityReport::where('presence_id', $validated['presence_id'])
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['presence_id' => 'You have already submitted a report for this activity.'])->withInput();
            }

            DB::beginTransaction();

            // Create report WITHOUT storing student_id to maintain anonymity
            ActivityReport::create([
                'presence_id' => $validated['presence_id'],
                'teacher_rating' => $validated['teacher_rating'],
                'feedback' => $validated['feedback'] ?? null,
                // Note: No student_id field - maintains anonymity
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Thank you! Your anonymous feedback has been submitted.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating report: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error submitting report: ' . $e->getMessage());
        }
    }

    /**
     * Edit is not allowed for anonymous reports.
     */
    public function edit(ActivityReport $activityReport)
    {
        return redirect()->route('dashboard')->withErrors('You cannot edit submitted reports.');
    }

    /**
     * Update is not allowed for anonymous reports.
     */
    public function update(Request $request, ActivityReport $activityReport)
    {
        return redirect()->route('dashboard')->withErrors('You cannot edit submitted reports.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityReport $activityReport)
    {
        try {
            // Only admin can delete reports
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('You do not have permission to delete reports.');
            }

            DB::beginTransaction();

            $activityReport->delete();

            DB::commit();

            return redirect()->route('activity-reports.index')->with('success', 'Report deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting report: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting report: ' . $e->getMessage());
        }
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
