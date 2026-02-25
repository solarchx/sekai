<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Activity;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Get announcements visible to the user based on scope
            $query = Announcement::query();
            
            if ($user->role === 'STUDENT') {
                $query->where(function ($q) use ($user) {
                    // PUBLIC: Everyone sees
                    $q->where('scope', 'PUBLIC');
                })
                ->orWhere(function ($q) use ($user) {
                    // SPECIFIC-CLASS: Students in the specific class
                    if ($user->class_id) {
                        $q->where('scope', 'SPECIFIC-CLASS')
                          ->where('class_id', $user->class_id);
                    }
                })
                ->orWhere(function ($q) use ($user) {
                    // SPECIFIC-GRADE: Students in the specific grade
                    if ($user->class_id && $user->class->grade_id) {
                        $q->where('scope', 'SPECIFIC-GRADE')
                          ->where('grade_id', $user->class->grade_id);
                    }
                });
            } else {
                // Teachers and above
                $teacherClasses = Activity::where('teacher_id', $user->id)->pluck('class_id')->unique();
                $homeroomedClass = $user->class_id ? [$user->class_id] : [];
                $allClasses = $teacherClasses->merge($homeroomedClass)->unique();

                $teacherGrades = Activity::where('teacher_id', $user->id)
                    ->join('classes', 'activities.class_id', '=', 'classes.id')
                    ->pluck('classes.grade_id')
                    ->unique();
                $homeroomedGrade = $user->class_id && $user->class->grade_id ? [$user->class->grade_id] : [];
                $allGrades = $teacherGrades->merge($homeroomedGrade)->unique();

                $query->where(function ($q) use ($user, $allClasses, $allGrades) {
                    $q->where('scope', 'PUBLIC')
                    ->orWhere('scope', 'TEACHERS')
                    ->orWhere(function ($nested) use ($allClasses) {
                        $nested->where('scope', 'SPECIFIC-CLASS')
                                ->whereIn('class_id', $allClasses);
                    })
                    ->orWhere(function ($nested) use ($allGrades) {
                        $nested->where('scope', 'SPECIFIC-GRADE')
                                ->whereIn('grade_id', $allGrades);
                    })
                    // CLASS-TAUGHT: show if sender is the current user? Actually it should be shown to all users in classes taught by the sender.
                    // But the sender is known. We need to show it to users who are in any class taught by the sender.
                    // This is more complex; we might need a subquery.
                    // For simplicity, we can treat CLASS-TAUGHT as visible to anyone who shares a class with the sender's taught classes.
                    // This can be done with a whereExists.
                    ->orWhere(function ($nested) use ($user) {
                        $nested->where('scope', 'CLASS-TAUGHT')
                                ->whereExists(function ($sub) use ($user) {
                                    $sub->select(DB::raw(1))
                                        ->from('activities')
                                        ->whereColumn('activities.teacher_id', 'announcements.sender_id')
                                        ->whereIn('activities.class_id', function ($q) use ($user) {
                                            $q->select('class_id')
                                            ->from('users')
                                            ->where('users.id', $user->id)
                                            ->whereNotNull('class_id');
                                        });
                                });
                    });
                });
            }
            
            $announcements = $query->with('sender', 'class', 'grade')
                                   ->latest()
                                   ->paginate(100);
            
            return view('announcements.index', compact('announcements'));
        } catch (\Exception $e) {
            Log::error('Error loading announcements: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading announcements: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $user = Auth::user();
            
            // Get activities where user teaches
            $activities = Activity::where('teacher_id', $user->id)
                ->with('class', 'subject')
                ->get();
            
            // Get classes from those activities
            $classes = Grade::all()->map(function ($grade) use ($activities) {
                $classIds = $activities->pluck('class_id')->unique();
                return $grade->classes()->whereIn('id', $classIds)->get();
            })->flatten();
            
            // Get grades where user teaches
            $teacherGrades = $activities
                ->map(fn($a) => $a->class->grade_id)
                ->unique();
            $userGrades = $user->class_id && $user->class->grade_id ? $teacherGrades->push($user->class->grade_id)->unique() : $teacherGrades;
            $grades = Grade::whereIn('id', $userGrades)->get();
            
            return view('announcements.create', compact('activities', 'classes', 'grades'));
        } catch (\Exception $e) {
            Log::error('Error loading announcement create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'title'    => 'required|max:255',
                'subtitle' => 'required|max:255',
                'content'  => 'required',
                'scope'    => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
                'grade_id' => 'nullable|exists:grades,id',
                'class_id' => 'nullable|exists:classes,id',
            ];

            $validated = $request->validate($rules);

            if ($validated['scope'] === 'SPECIFIC-CLASS' && empty($validated['class_id'])) {
                return back()->withErrors(['class_id' => 'Class is required for SPECIFIC-CLASS scope.']);
            }
            if ($validated['scope'] === 'SPECIFIC-GRADE' && empty($validated['grade_id'])) {
                return back()->withErrors(['grade_id' => 'Grade is required for SPECIFIC-GRADE scope.']);
            }

            // CLASS-TAUGHT should have no stored target
            if ($validated['scope'] === 'CLASS-TAUGHT') {
                $validated['class_id'] = null;
                $validated['grade_id'] = null;
            }
            DB::beginTransaction();

            $validated['sender_id'] = Auth::id();
            Announcement::create($validated);

            DB::commit();

            return redirect()->route('announcements.index')->with('success', 'Announcement created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating announcement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating announcement: ' . $e->getMessage());
        }
    }

    public function edit(Announcement $announcement)
    {
        try {
            if (Auth::id() !== $announcement->sender_id && Auth::user()->role !== 'ADMIN') {
                return redirect()->route('wrongway');
            }

            $user = Auth::user();
            
            // Get classes where user teaches or is homeroom teacher
            $taughtClasses = Activity::where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique();
            $homeroomedClass = $user->class_id ? [$user->class_id] : [];
            $userClasses = array_merge($taughtClasses->toArray(), $homeroomedClass);
            $classes = DB::table('classes')
                ->whereIn('id', $userClasses)
                ->get();
            
            // Get grades where user teaches or is assigned
            $teacherGrades = Activity::where('teacher_id', $user->id)
                ->join('classes', 'activities.class_id', '=', 'classes.id')
                ->pluck('classes.grade_id')
                ->unique();
            $homeroomedGrade = $user->class_id && $user->class->grade_id ? [$user->class->grade_id] : [];
            $userGrades = $teacherGrades->merge($homeroomedGrade)->unique();
            $grades = Grade::whereIn('id', $userGrades)->get();

            return view('announcements.edit', compact('announcement', 'classes', 'grades'));
        } catch (\Exception $e) {
            Log::error('Error loading announcement edit: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Announcement $announcement)
    {
        try {
            if (Auth::id() !== $announcement->sender_id && Auth::user()->role !== 'ADMIN') {
                return redirect()->route('wrongway');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'content' => 'required|string',
                'scope' => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
                'class_id' => 'nullable|exists:classes,id',
                'grade_id' => 'nullable|exists:grades,id',
            ]);

            DB::beginTransaction();
            $announcement->update($validated);
            DB::commit();

            return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating announcement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating announcement: ' . $e->getMessage());
        }
    }

    public function destroy(Announcement $announcement)
    {
        if (auth()->id() !== $announcement->sender_id && auth()->user()->role !== 'ADMIN') {
            abort(403);
        }
        $announcement->delete(); // soft delete
        return redirect()->route('announcements.index')->with('success', 'Announcement deleted.');
    }

    public function restore(Announcement $announcement)
    {
        try {
            if (Auth::user()->role !== 'ADMIN') {
                return redirect()->route('wrongway');
            }

            DB::beginTransaction();
            $announcement->restore();
            DB::commit();

            return redirect()->back()->with('success', 'Announcement restored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring announcement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring announcement: ' . $e->getMessage());
        }
    }
}

