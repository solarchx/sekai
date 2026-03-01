<?php

namespace App\Http\Controllers;

use App\Models\ActivityPresence;
use App\Models\ActivityForm;
use App\Models\Activity;
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
            $user = auth()->user();
            $showDeleted = $request->has('show_deleted') && $user->role === 'ADMIN';
            $activityId = $request->query('activity_id');
            $formId = $request->query('form_id');

            if ($user->role === 'TEACHER') {
                $activities = Activity::where('teacher_id', $user->id)
                    ->with('subject', 'class')
                    ->get();
            } elseif (in_array($user->role, ['VP', 'ADMIN'])) {
                $activities = Activity::with('subject', 'class')->get();
            } else {
                $activities = collect();
            }

            $selectedActivity = null;
            $forms = collect();
            $selectedForm = null;
            $students = collect();
            $presences = collect();

            if ($activityId) {
                $selectedActivity = $activities->firstWhere('id', $activityId);
                if ($selectedActivity) {
                    $forms = $selectedActivity->forms()->orderBy('activity_date', 'desc')->get();
                    if ($formId) {
                        $selectedForm = $forms->firstWhere('id', $formId);
                        if ($selectedForm) {
                            $students = $selectedForm->activity->class->students()
                                ->where('role', 'STUDENT')
                                ->where('deleted_at', null)
                                ->orderBy('student_order')
                                ->get(['id', 'name', 'identifier']);

                            $presences = ActivityPresence::where('form_id', $formId)
                                ->with('student')
                                ->get()
                                ->keyBy('student_id');
                        }
                    }
                }
            }

            return view('activity-presences.index', compact(
                'activities', 'selectedActivity', 'forms', 'selectedForm',
                'students', 'presences', 'showDeleted'
            ));
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
                $timezone = config('app.timezone');
                $formDate = $form->activity_date;
                $timeBegin = $activity->period->time_begin;
                $timeEnd   = $activity->period->time_end;

                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $formDate->format('Y-m-d') . ' ' . $timeBegin, $timezone);
                $endDateTime   = Carbon::createFromFormat('Y-m-d H:i:s', $formDate->format('Y-m-d') . ' ' . $timeEnd, $timezone);
                if (strlen($timeBegin) == 5) {
                    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $formDate->format('Y-m-d') . ' ' . $timeBegin, $timezone);
                    $endDateTime   = Carbon::createFromFormat('Y-m-d H:i', $formDate->format('Y-m-d') . ' ' . $timeEnd, $timezone);
                }

                $submissionStart = $startDateTime->copy()->subMinutes(15);
                $submissionEnd   = $endDateTime->copy()->addMinutes(15);
                $now = Carbon::now($timezone);

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