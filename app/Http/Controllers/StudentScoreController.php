<?php

namespace App\Http\Controllers;

use App\Models\StudentScore;
use App\Models\Activity;
use App\Models\User;
use App\Models\ScoreDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentScoreController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = StudentScore::with('activity.subject', 'activity.teacher', 'activity.class', 'student');
            
            if ($showDeleted) {
                $scores = $query->onlyTrashed()->paginate(100);
            } else {
                $scores = $query->paginate(100);
            }
            
            return view('student-scores.index', compact('scores', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading student scores: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading student scores: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $activities = Activity::with('subject', 'teacher', 'class')
                ->where('deleted_at', null)
                ->get();
            
            if ($activities->isEmpty()) {
                return redirect()->route('student-scores.index')->withErrors('No activities available.');
            }
            
            return view('student-scores.create', compact('activities'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'scores' => 'required|array|min:1',
                'scores.*.student_id' => 'required|exists:users,id',
                'scores.*.distribution_name' => 'required|string|max:255',
                'scores.*.score' => 'required|integer|between:0,100',
            ], [
                'activity_id.required' => 'Activity is required.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'scores.required' => 'At least one student score is required.',
                'scores.min' => 'At least one student score is required.',
                'scores.*.student_id.required' => 'Student is required.',
                'scores.*.student_id.exists' => 'Selected student does not exist.',
                'scores.*.distribution_name.required' => 'Score distribution name is required.',
                'scores.*.score.required' => 'Score is required.',
                'scores.*.score.integer' => 'Score must be a number.',
                'scores.*.score.between' => 'Score must be between 0 and 100.',
            ]);

            $activity = Activity::find($validated['activity_id']);

            
            foreach ($validated['scores'] as $scoreData) {
                $isEnrolled = $activity->students()
                    ->where('users.id', $scoreData['student_id'])
                    ->exists();

                if (!$isEnrolled) {
                    return back()->withErrors(['scores' => 'One or more students are not enrolled in this activity.'])->withInput();
                }

                
                $distribution = ScoreDistribution::where('activity_id', $validated['activity_id'])
                    ->where('name', $scoreData['distribution_name'])
                    ->where('deleted_at', null)
                    ->first();

                if (!$distribution) {
                    return back()->withErrors(['scores' => "Score distribution '{$scoreData['distribution_name']}' does not exist for this activity."])->withInput();
                }

                
                $exists = StudentScore::where('activity_id', $validated['activity_id'])
                    ->where('student_id', $scoreData['student_id'])
                    ->where('name', $scoreData['distribution_name'])
                    ->where('deleted_at', null)
                    ->exists();

                if ($exists) {
                    $student = User::find($scoreData['student_id']);
                    return back()->withErrors(['scores' => "Score already exists for {$student->name} on {$scoreData['distribution_name']}."])->withInput();
                }
            }

            DB::beginTransaction();

            foreach ($validated['scores'] as $scoreData) {
                StudentScore::create([
                    'activity_id' => $validated['activity_id'],
                    'student_id' => $scoreData['student_id'],
                    'name' => $scoreData['distribution_name'],
                    'score' => $scoreData['score'],
                ]);
            }

            DB::commit();

            return redirect()->route('student-scores.index')->with('success', 'Student scores created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating student scores: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating student scores: ' . $e->getMessage());
        }
    }

    public function edit(StudentScore $studentScore)
    {
        try {
            $activities = Activity::with('subject', 'teacher', 'class')
                ->where('deleted_at', null)
                ->get();
            
            
            $distributions = ScoreDistribution::where('activity_id', $studentScore->activity_id)
                ->where('deleted_at', null)
                ->get();
            
            return view('student-scores.edit', compact('studentScore', 'activities', 'distributions'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, StudentScore $studentScore)
    {
        try {
            $validated = $request->validate([
                'activity_id' => 'required|exists:activities,id',
                'student_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'score' => 'required|integer|between:0,100',
            ], [
                'activity_id.required' => 'Activity is required.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'student_id.required' => 'Student is required.',
                'student_id.exists' => 'Selected student does not exist.',
                'name.required' => 'Score distribution name is required.',
                'score.required' => 'Score is required.',
                'score.integer' => 'Score must be a number.',
                'score.between' => 'Score must be between 0 and 100.',
            ]);

            
            $activity = Activity::find($validated['activity_id']);
            $isEnrolled = $activity->students()
                ->where('users.id', $validated['student_id'])
                ->exists();

            if (!$isEnrolled) {
                return back()->withErrors('Student is not enrolled in this activity.')->withInput();
            }

            
            $exists = StudentScore::where('activity_id', $validated['activity_id'])
                ->where('student_id', $validated['student_id'])
                ->where('name', $validated['name'])
                ->where('id', '!=', $studentScore->id)
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors('A score with this name already exists for this student in this activity.')->withInput();
            }

            DB::beginTransaction();

            $studentScore->update($validated);

            DB::commit();

            return redirect()->route('student-scores.index')->with('success', 'Student score updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating student score: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating student score: ' . $e->getMessage());
        }
    }

    public function destroy(StudentScore $studentScore)
    {
        try {
            DB::beginTransaction();

            $studentScore->delete();

            DB::commit();

            return redirect()->route('student-scores.index')->with('success', 'Student score deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting student score: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting student score: ' . $e->getMessage());
        }
    }

    
    public function restore(StudentScore $studentScore)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $studentScore->restore();

            return redirect()->route('student-scores.index')->with('success', 'Student score restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring student score: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring student score: ' . $e->getMessage());
        }
    }
}
