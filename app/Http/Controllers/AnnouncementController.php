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
                    // PUBLIC: Everyone sees
                    $q->where('scope', 'PUBLIC')
                    // TEACHERS: All teachers+ see
                    ->orWhere('scope', 'TEACHERS')
                    // CLASS-TAUGHT: Teachers see announcements for all their classes
                    ->orWhere(function ($nested) use ($allClasses) {
                        if ($allClasses->count() > 0) {
                            $nested->where('scope', 'CLASS-TAUGHT')
                                   ->whereIn('class_id', $allClasses);
                        }
                    })
                    // SPECIFIC-CLASS: Teachers see for their specific classes
                    ->orWhere(function ($nested) use ($allClasses) {
                        if ($allClasses->count() > 0) {
                            $nested->where('scope', 'SPECIFIC-CLASS')
                                   ->whereIn('class_id', $allClasses);
                        }
                    })
                    // SPECIFIC-GRADE: Teachers see for their grades
                    ->orWhere(function ($nested) use ($allGrades) {
                        if ($allGrades->count() > 0) {
                            $nested->where('scope', 'SPECIFIC-GRADE')
                                   ->whereIn('grade_id', $allGrades);
                        }
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
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'content' => 'required|string',
                'scope' => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
                'class_id' => 'nullable|exists:classes,id',
                'grade_id' => 'nullable|exists:grades,id',
            ], [
                'title.required' => 'Title is required.',
                'subtitle.required' => 'Subtitle is required.',
                'content.required' => 'Content is required.',
                'scope.required' => 'Scope is required.',
                'scope.in' => 'Invalid scope selected.',
                'class_id.exists' => 'Selected class does not exist.',
                'grade_id.exists' => 'Selected grade does not exist.',
            ]);

            // Validate scope requirements
            if (in_array($validated['scope'], ['SPECIFIC-CLASS', 'CLASS-TAUGHT'])) {
                if (!$validated['class_id']) {
                    return back()->withErrors(['class_id' => 'Class is required for this scope.'])->withInput();
                }
            }
            if ($validated['scope'] === 'SPECIFIC-GRADE') {
                if (!$validated['grade_id']) {
                    return back()->withErrors(['grade_id' => 'Grade is required for this scope.'])->withInput();
                }
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
        try {
            // Teachers+ can only delete their own; admins can softDelete any
            if (Auth::id() !== $announcement->sender_id) {
                if (Auth::user()->role === 'ADMIN') {
                    $announcement->delete(); // Soft delete for admin
                } else {
                    return redirect()->route('wrongway');
                }
            } else {
                // Sender can permanently delete their own
                $announcement->forceDelete();
            }

            return redirect()->route('announcements.index')->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting announcement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting announcement: ' . $e->getMessage());
        }
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

