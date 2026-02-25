<?php

namespace App\Http\Controllers;

use App\Models\ActivityForm;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActivityFormController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = ActivityForm::with('activity.subject', 'activity.teacher', 'activity.class');
            
            if ($showDeleted) {
                $forms = $query->onlyTrashed()->paginate(100);
            } else {
                $forms = $query->paginate(100);
            }
            
            return view('activity-forms.index', compact('forms', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading forms: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading forms: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $activities = Activity::with('subject', 'teacher', 'class')
                ->where('deleted_at', null)
                ->get();
            
            if ($activities->isEmpty()) {
                return redirect()->route('activity-forms.index')->withErrors('No activities available.');
            }
            
            return view('activity-forms.create', compact('activities'));
        } catch (\Exception $e) {
            Log::error('Error loading form create: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'activity_date' => 'required|date|after_or_equal:today',
            ], [
                'activity_id.required' => 'Activity is required.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'activity_date.required' => 'Activity date is required.',
                'activity_date.date' => 'Activity date must be a valid date.',
                'activity_date.after_or_equal' => 'Activity date must be today or in the future.',
            ]);

            $activity = Activity::find($validated['activity_id']);
            $formDate = Carbon::createFromFormat('Y-m-d', $validated['activity_date']);

            // Verify date matches activity's lesson period weekday
            $activityWeekday = $activity->period->weekday;
            $dateWeekday = $formDate->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            
            // Adjust weekday comparison (Laravel Carbon: Sunday=0, Monday=1; Database: Monday=0, Sunday=6)
            $carbonWeekday = ($dateWeekday + 6) % 7;
            
            if ($carbonWeekday !== $activityWeekday) {
                return back()->withErrors(['activity_date' => "Activity date must be on a {$this->getWeekdayName($activityWeekday)}."])->withInput();
            }

            // Check for duplicate
            $exists = ActivityForm::where('activity_id', $validated['activity_id'])
                ->where('activity_date', $validated['activity_date'])
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['activity_date' => 'A form already exists for this activity on this date.'])->withInput();
            }

            DB::beginTransaction();

            ActivityForm::create($validated);

            DB::commit();

            return redirect()->route('activity-forms.index')->with('success', 'Activity form created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating form: ' . $e->getMessage());
        }
    }

    public function show(ActivityForm $activityForm)
    {
        try {
            $activityForm->load('activity.subject', 'activity.teacher', 'activity.class', 'activity.period');
            $form = $activityForm;
            $students = $activityForm->activity->class->students()
                ->where('role', 'STUDENT')
                ->where('deleted_at', null)
                ->orderBy('student_order')
                ->get();
            
            return view('activity-forms.show', compact('activityForm', 'form', 'students'));
        } catch (\Exception $e) {
            Log::error('Error loading form details: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form details: ' . $e->getMessage());
        }
    }

    public function edit(ActivityForm $activityForm)
    {
        try {
            $activities = Activity::with('subject', 'teacher', 'class')
                ->where('deleted_at', null)
                ->get();
            
            return view('activity-forms.edit', compact('activityForm', 'activities'));
        } catch (\Exception $e) {
            Log::error('Error loading form edit: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ActivityForm $activityForm)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'activity_date' => 'required|date|after_or_equal:today',
            ], [
                'activity_id.required' => 'Activity is required.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'activity_date.required' => 'Activity date is required.',
                'activity_date.date' => 'Activity date must be a valid date.',
                'activity_date.after_or_equal' => 'Activity date must be today or in the future.',
            ]);

            $activity = Activity::find($validated['activity_id']);
            $formDate = Carbon::createFromFormat('Y-m-d', $validated['activity_date']);

            // Verify date matches activity's lesson period weekday
            $activityWeekday = $activity->period->weekday;
            $dateWeekday = $formDate->dayOfWeek;
            $carbonWeekday = ($dateWeekday + 6) % 7;
            
            if ($carbonWeekday !== $activityWeekday) {
                return back()->withErrors(['activity_date' => "Activity date must be on a {$this->getWeekdayName($activityWeekday)}."])->withInput();
            }

            // Check for duplicates (excluding current)
            $exists = ActivityForm::where('activity_id', $validated['activity_id'])
                ->where('activity_date', $validated['activity_date'])
                ->where('id', '!=', $activityForm->id)
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['activity_date' => 'A form already exists for this activity on this date.'])->withInput();
            }

            DB::beginTransaction();

            $activityForm->update($validated);

            DB::commit();

            return redirect()->route('activity-forms.index')->with('success', 'Activity form updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating form: ' . $e->getMessage());
        }
    }

    public function destroy(ActivityForm $activityForm)
    {
        try {
            DB::beginTransaction();

            // Delete all presences associated with this form
            $activityForm->presences()->delete();

            $activityForm->delete();

            DB::commit();

            return redirect()->route('activity-forms.index')->with('success', 'Activity form deleted successfully (all presences removed).');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting form: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted form (admin only).
     */
    public function restore(ActivityForm $activityForm)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $activityForm->restore();

            return redirect()->route('activity-forms.index')->with('success', 'Form restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring form: ' . $e->getMessage());
        }
    }

    /**
     * Get weekday name from weekday number (0-6, Monday-Sunday).
     */
    private function getWeekdayName($weekday): string
    {
        $days = [
            0 => 'Monday',
            1 => 'Tuesday',
            2 => 'Wednesday',
            3 => 'Thursday',
            4 => 'Friday',
            5 => 'Saturday',
            6 => 'Sunday',
        ];
        return $days[$weekday] ?? 'Unknown';
    }
}
