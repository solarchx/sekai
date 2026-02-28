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
            $formId = $request->query('form_id');

            if ($formId) {
                $form = ActivityForm::with('activity.class')->findOrFail($formId);
                $students = $form->activity->class->students()
                    ->where('role', 'STUDENT')
                    ->where('deleted_at', null)
                    ->orderBy('student_order')
                    ->get(['id', 'name', 'identifier']);

                $presences = ActivityPresence::where('form_id', $formId)
                    ->with('student')
                    ->get()
                    ->keyBy('student_id');

                return view('activity-presences.index', compact('form', 'students', 'presences', 'showDeleted'));
            }

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

    public function create(Request $request)
    {
        try {
            $formId = $request->query('form_id');
            $studentId = $request->query('student_id');

            if (!$formId || !$studentId) {
                return redirect()->route('activity-presences.index')->withErrors('Form and student are required.');
            }

            $form = ActivityForm::with('activity.class')->findOrFail($formId);
            $student = User::findOrFail($studentId);

            if ($student->class_id != $form->activity->class_id) {
                return redirect()->route('activity-presences.index')->withErrors('Student does not belong to this class.');
            }

            return view('activity-presences.create', compact('form', 'student'));
        } catch (\Exception $e) {
            Log::error('Error loading presence create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'form_id'    => 'required|exists:activity_forms,id',
                'student_id' => 'required|exists:users,id',
                'score'      => 'required|integer|between:0,3',
                'location'   => 'required|string|max:255',
            ]);

            $form = ActivityForm::find($validated['form_id']);
            $activity = $form->activity;

            if (auth()->user()->role === 'STUDENT') {
                $formDate = $form->activity_date->format('Y-m-d');
                $startDateTime = Carbon::parse($formDate . ' ' . $activity->period->time_begin);
                $endDateTime = Carbon::parse($formDate . ' ' . $activity->period->time_end);
                
                $submissionStart = $startDateTime->copy()->subMinutes(15);
                $submissionEnd = $endDateTime->copy()->addMinutes(15);
                $now = Carbon::now();

                if ($now->lt($submissionStart) || $now->gt($submissionEnd)) {
                    return back()->withErrors(['student_id' => "Submission window closed."])->withInput();
                }
            }

            $exists = ActivityPresence::where('form_id', $validated['form_id'])
                ->where('student_id', $validated['student_id'])
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['student_id' => 'This student already has a presence record.'])->withInput();
            }

            $isEnrolled = $activity->students()->where('users.id', $validated['student_id'])->exists();
            if (!$isEnrolled) {
                return back()->withErrors(['student_id' => 'Student is not enrolled in this activity.'])->withInput();
            }

            DB::beginTransaction();
            ActivityPresence::create($validated);
            DB::commit();

            return redirect()->route('activity-presences.index', ['form_id' => $validated['form_id']])
                ->with('success', 'Presence record created successfully.');
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
            $form = $activityPresence->form;
            $student = $activityPresence->student;
            return view('activity-presences.edit', compact('activityPresence', 'form', 'student'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ActivityPresence $activityPresence)
    {
        try {
            $validated = $request->validate([
                'score'    => 'required|integer|between:0,3',
                'location' => 'required|string|max:255',
            ]);

            DB::beginTransaction();
            $activityPresence->update($validated);
            DB::commit();

            return redirect()->route('activity-presences.index', ['form_id' => $activityPresence->form_id])
                ->with('success', 'Presence record updated successfully.');
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
            $activityPresence->report()->delete();
            $activityPresence->delete();
            DB::commit();

            return redirect()->route('activity-presences.index', ['form_id' => $activityPresence->form_id])
                ->with('success', 'Presence record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting presence: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting presence: ' . $e->getMessage());
        }
    }

    public function restore(ActivityPresence $activityPresence)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }
            $activityPresence->restore();
            return redirect()->route('activity-presences.index', ['form_id' => $activityPresence->form_id])
                ->with('success', 'Presence restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring presence: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring presence: ' . $e->getMessage());
        }
    }
}