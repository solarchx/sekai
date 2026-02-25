<?php

namespace App\Http\Controllers;

use App\Models\ActivityPresence;
use App\Models\ActivityForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActivityPresenceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = ActivityPresence::with('form', 'student');
            
            if ($showDeleted) {
                $presences = $query->onlyTrashed()->paginate(100);
            } else {
                $presences = $query->paginate(100);
            }
            
            return view('activity-presences.index', compact('presences', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading presences: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading presences: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $forms = ActivityForm::with('activity.subject', 'activity.teacher', 'activity.class', 'activity.students')
                ->where('deleted_at', null)
                ->get();
            
            if ($forms->isEmpty()) {
                return redirect()->route('activity-presences.index')->withErrors('No activity forms available.');
            }
            
            return view('activity-presences.create', compact('forms'));
        } catch (\Exception $e) {
            Log::error('Error loading presence create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'form_id' => 'required|exists:activity_forms,id',
                'student_id' => 'required|exists:users,id',
                'score' => 'required|integer|between:0,3',
                'location' => 'required|string|max:255',
            ], [
                'form_id.required' => 'Activity form is required.',
                'form_id.exists' => 'Selected form does not exist.',
                'student_id.required' => 'Student is required.',
                'student_id.exists' => 'Selected student does not exist.',
                'score.required' => 'Score is required.',
                'score.integer' => 'Score must be a number.',
                'score.between' => 'Score must be between 0 and 3.',
                'location.required' => 'Location (GPS) is required.',
                'location.max' => 'Location must not exceed 255 characters.',
            ]);

            $form = ActivityForm::find($validated['form_id']);
            $activity = $form->activity;

            // Check if submission is within time window (15 min before to 15 min after activity time)
            $activityStart = Carbon::createFromFormat('H:i', $activity->period->time_begin);
            $activityEnd = Carbon::createFromFormat('H:i', $activity->period->time_end);
            
            $submissionStart = $activityStart->copy()->subMinutes(15);
            $submissionEnd = $activityEnd->copy()->addMinutes(15);
            
            $now = Carbon::now();
            
            if ($now->lt($submissionStart) || $now->gt($submissionEnd)) {
                return back()->withErrors(['student_id' => "Submission window closed. Available from {$submissionStart->format('H:i')} to {$submissionEnd->format('H:i')}."]);
            }

            // Check for duplicate
            $exists = ActivityPresence::where('form_id', $validated['form_id'])
                ->where('student_id', $validated['student_id'])
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['student_id' => 'This student already has a presence record for this form.']);
            }

            // Verify student is enrolled in the activity
            $isEnrolled = $activity->students()
                ->where('users.id', $validated['student_id'])
                ->exists();

            if (!$isEnrolled) {
                return back()->withErrors(['student_id' => 'Selected student is not enrolled in this activity.']);
            }

            DB::beginTransaction();

            ActivityPresence::create($validated);

            DB::commit();

            return redirect()->route('activity-presences.index')->with('success', 'Presence record created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating presence: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating presence: ' . $e->getMessage());
        }
    }

    public function edit(ActivityPresence $activityPresence)
    {
        try {
            $forms = ActivityForm::with('activity.subject', 'activity.teacher', 'activity.class', 'activity.students')
                ->where('deleted_at', null)
                ->get();
            
            return view('activity-presences.edit', compact('activityPresence', 'forms'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ActivityPresence $activityPresence)
    {
        try {
            $validated = $request->validate([
                'form_id' => 'required|exists:activity_forms,id',
                'student_id' => 'required|exists:users,id',
                'score' => 'required|integer|between:0,3',
                'location' => 'required|string|max:255',
            ], [
                'form_id.required' => 'Activity form is required.',
                'form_id.exists' => 'Selected form does not exist.',
                'student_id.required' => 'Student is required.',
                'student_id.exists' => 'Selected student does not exist.',
                'score.required' => 'Score is required.',
                'score.integer' => 'Score must be a number.',
                'score.between' => 'Score must be between 0 and 3.',
                'location.required' => 'Location (GPS) is required.',
                'location.max' => 'Location must not exceed 255 characters.',
            ]);

            // Check for duplicates (excluding current record)
            $exists = ActivityPresence::where('form_id', $validated['form_id'])
                ->where('student_id', $validated['student_id'])
                ->where('id', '!=', $activityPresence->id)
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['student_id' => 'This student already has a presence record for this form.']);
            }

            DB::beginTransaction();

            $activityPresence->update($validated);

            DB::commit();

            return redirect()->route('activity-presences.index')->with('success', 'Presence record updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating presence: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating presence: ' . $e->getMessage());
        }
    }

    public function destroy(ActivityPresence $activityPresence)
    {
        try {
            DB::beginTransaction();

            // Delete associated activity report
            $activityPresence->report()->delete();

            $activityPresence->delete();

            DB::commit();

            return redirect()->route('activity-presences.index')->with('success', 'Presence record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting presence: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting presence: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted presence (admin only).
     */
    public function restore(ActivityPresence $activityPresence)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $activityPresence->restore();

            return redirect()->route('activity-presences.index')->with('success', 'Presence restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring presence: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring presence: ' . $e->getMessage());
        }
    }
}
