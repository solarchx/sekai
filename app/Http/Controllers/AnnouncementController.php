<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Activity;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get announcements visible to the user
        $query = Announcement::query();
        
        if ($user->role === 'STUDENT') {
            // Students see: PUBLIC, SPECIFIC-CLASS, SPECIFIC-GRADE, TEACHERS (if they are a teacher, but they're not)
            $query->where('scope', 'PUBLIC')
                  ->orWhere(function ($q) use ($user) {
                      if ($user->class_id) {
                          $q->where('scope', 'SPECIFIC-CLASS')
                            ->where('activity_id', null)
                            ->orWhere(function ($nested) use ($user) {
                                $nested->where('scope', 'SPECIFIC-CLASS')
                                       ->whereIn('activity_id', Activity::where('class_id', $user->class_id)->pluck('id'));
                            });
                      }
                  })
                  ->orWhere(function ($q) use ($user) {
                      if ($user->class_id) {
                          $q->where('scope', 'SPECIFIC-GRADE')
                            ->where('grade_id', $user->class->grade_id ?? null);
                      }
                  });
        } else {
            // Teachers+ see: PUBLIC, CLASS-TAUGHT, SPECIFIC-CLASS (if applicable), SPECIFIC-GRADE (if applicable), TEACHERS
            $teacherActivities = Activity::where('teacher_id', $user->id)->pluck('id');
            $teacherClasses = Activity::where('teacher_id', $user->id)->pluck('class_id')->unique();
            $teacherGrades = $user->class_id ? [$user->class->grade_id] : [];
            
            $query->where('scope', 'PUBLIC')
                  ->orWhere('scope', 'TEACHERS')
                  ->orWhere('scope', 'CLASS-TAUGHT')
                  ->orWhere(function ($q) use ($teacherActivities) {
                      $q->where('scope', 'SPECIFIC-CLASS')
                        ->whereIn('activity_id', $teacherActivities);
                  })
                  ->orWhere(function ($q) use ($teacherGrades) {
                      $q->where('scope', 'SPECIFIC-GRADE')
                        ->whereIn('grade_id', $teacherGrades);
                  });
        }
        
        $announcements = $query->with('sender', 'activity.subject', 'activity.class', 'grade')
                               ->latest()
                               ->paginate(100);
        
        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get activities taught by this user
        $activities = Activity::where('teacher_id', $user->id)->with('subject', 'class')->get();
        
        // Get grades where user teaches or is assigned
        $grades = Grade::all();
        
        return view('announcements.create', compact('activities', 'grades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'content' => 'required|string',
            'scope' => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
            'activity_id' => 'nullable|exists:activities,id',
            'grade_id' => 'nullable|exists:grades,id',
        ]);

        $validated['sender_id'] = Auth::id();

        Announcement::create($validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        if (Auth::id() !== $announcement->sender_id && Auth::user()->role !== 'ADMIN') {
            return redirect()->route('wrongway');
        }

        $user = Auth::user();
        $activities = Activity::where('teacher_id', $user->id)->with('subject', 'class')->get();
        $grades = Grade::all();

        return view('announcements.edit', compact('announcement', 'activities', 'grades'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if (Auth::id() !== $announcement->sender_id && Auth::user()->role !== 'ADMIN') {
            return redirect()->route('wrongway');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'content' => 'required|string',
            'scope' => 'required|in:SPECIFIC-CLASS,CLASS-TAUGHT,SPECIFIC-GRADE,TEACHERS,PUBLIC',
            'activity_id' => 'nullable|exists:activities,id',
            'grade_id' => 'nullable|exists:grades,id',
        ]);

        $announcement->update($validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if (Auth::id() !== $announcement->sender_id && Auth::user()->role !== 'ADMIN') {
            return redirect()->route('wrongway');
        }

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}
