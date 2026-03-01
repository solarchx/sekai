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
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = Announcement::query();
            
            $showDeleted = $request->has('show_deleted') && $user->role === 'ADMIN';
            if ($showDeleted) {
                $query = $query->onlyTrashed();
            }

            if ($user->role === 'STUDENT') {
                $query->where(function ($q) use ($user) {
                    
                    $q->where('scope', 'PUBLIC')
                      
                      ->orWhere(function ($sub) use ($user) {
                          if ($user->class_id) {
                              $sub->where('scope', 'SPECIFIC-CLASS')
                                  ->whereHas('activity', function ($act) use ($user) {
                                      $act->where('class_id', $user->class_id);
                                  });
                          }
                      })
                      
                      ->orWhere(function ($sub) use ($user) {
                          if ($user->class_id && $user->class->grade_id) {
                              $sub->where('scope', 'SPECIFIC-GRADE')
                                  ->where('grade_id', $user->class->grade_id);
                          }
                      });
                });
            } else {
                if (in_array($user->role, ['VP', 'ADMIN'])) {
                    $announcements = Announcement::all();
                } else {
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
                        
                        ->orWhere(function ($sub) use ($allClasses) {
                            if ($allClasses->isNotEmpty()) {
                                $sub->where('scope', 'SPECIFIC-CLASS')
                                    ->whereHas('activity', function ($act) use ($allClasses) {
                                        $act->whereIn('class_id', $allClasses);
                                    });
                            }
                        })
                        
                        ->orWhere(function ($sub) use ($user) {
                            $sub->where('scope', 'CLASS-TAUGHT')
                                ->whereExists(function ($exist) use ($user) {
                                    $exist->select(DB::raw(1))
                                        ->from('activities')
                                        ->whereColumn('activities.teacher_id', 'announcements.sender_id')
                                        ->whereIn('activities.class_id', function ($q) use ($user) {
                                            $q->select('class_id')
                                                ->from('users')
                                                ->where('users.id', $user->id)
                                                ->whereNotNull('class_id');
                                        });
                                });
                        })
                        
                        ->orWhere(function ($sub) use ($allGrades) {
                            if ($allGrades->isNotEmpty()) {
                                $sub->where('scope', 'SPECIFIC-GRADE')
                                    ->whereIn('grade_id', $allGrades);
                            }
                        });
                    });
                }
                
                $announcements = $query->with('sender', 'activity', 'grade')
                                    ->latest()
                                    ->paginate(100);
            }
            
            return view('announcements.index', compact('announcements', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading announcements: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading announcements: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $user = Auth::user();

            if (in_array($user->role, ['VP', 'ADMIN'])) {
                $activities = Activity::all();
                $grades = Grade::all();
            } else {
                $activities = Activity::where('teacher_id', $user->id)
                    ->with('class', 'subject')
                    ->get();
                
                $teacherGrades = $activities->map(fn($a) => $a->class->grade_id)->unique();
                $homeroomedGrade = $user->class_id && $user->class->grade_id ? [$user->class->grade_id] : [];
                $grades = Grade::whereIn('id', $teacherGrades->merge($homeroomedGrade)->unique())->get();
            }

            return view('announcements.create', compact('activities', 'grades'));
        } catch (\Exception $e) {
            Log::error('Error loading announcement create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'    => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'content'  => 'required|string',
                'scope'    => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
                'activity_id' => 'nullable|exists:activities,id',
                'grade_id'    => 'nullable|exists:grades,id',
            ], [
                'title.required'    => 'Title is required.',
                'subtitle.required' => 'Subtitle is required.',
                'content.required'  => 'Content is required.',
                'scope.required'    => 'Scope is required.',
                'scope.in'          => 'Invalid scope selected.',
                'activity_id.exists' => 'Selected activity does not exist.',
                'grade_id.exists'   => 'Selected grade does not exist.',
            ]);

            
            if ($validated['scope'] === 'SPECIFIC-CLASS' && !$validated['activity_id']) {
                return back()->withErrors(['activity_id' => 'Class (activity) is required for this scope.'])->withInput();
            }
            if ($validated['scope'] === 'SPECIFIC-GRADE' && !$validated['grade_id']) {
                return back()->withErrors(['grade_id' => 'Grade is required for this scope.'])->withInput();
            }
            
            if ($validated['scope'] === 'CLASS-TAUGHT') {
                $validated['activity_id'] = null;
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

            if (in_array($user->role, ['VP', 'ADMIN'])) {
                $activities = Activity::all();
                $grades = Grade::all();
            } else {
                $activities = Activity::where('teacher_id', $user->id)
                    ->with('class', 'subject')
                    ->get();

                $teacherGrades = $activities->map(fn($a) => $a->class->grade_id)->unique();
                $homeroomedGrade = $user->class_id && $user->class->grade_id ? [$user->class->grade_id] : [];
                $grades = Grade::whereIn('id', $teacherGrades->merge($homeroomedGrade)->unique())->get();
            }

            return view('announcements.edit', compact('announcement', 'activities', 'grades'));
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
                'title'    => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'content'  => 'required|string',
                'scope'    => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
                'activity_id' => 'nullable|exists:activities,id',
                'grade_id'    => 'nullable|exists:grades,id',
            ]);

            if ($validated['scope'] === 'SPECIFIC-CLASS' && !$validated['activity_id']) {
                return back()->withErrors(['activity_id' => 'Class (activity) is required for this scope.'])->withInput();
            }
            if ($validated['scope'] === 'SPECIFIC-GRADE' && !$validated['grade_id']) {
                return back()->withErrors(['grade_id' => 'Grade is required for this scope.'])->withInput();
            }
            if ($validated['scope'] === 'CLASS-TAUGHT') {
                $validated['activity_id'] = null;
                $validated['grade_id'] = null;
            }

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
            
            if (Auth::id() !== $announcement->sender_id && Auth::user()->role !== 'ADMIN') {
                return redirect()->route('wrongway');
            }

            $announcement->delete();

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

            $announcement->restore();

            return redirect()->back()->with('success', 'Announcement restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring announcement: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring announcement: ' . $e->getMessage());
        }
    }
}