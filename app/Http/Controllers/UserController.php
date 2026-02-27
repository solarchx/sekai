<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = User::orderBy('role')->orderBy('name');
            
            if ($showDeleted) {
                $users = $query->onlyTrashed()->paginate(100);
            } else {
                $users = $query->paginate(100);
            }
            
            return view('admin.users.index', compact('users', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading users: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading users: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $classes = SchoolClass::where('capacity', '>', 0)->get();
            return view('admin.users.create', compact('classes'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading create form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'       => 'required|string|max:255',
                'email'      => 'required|string|email|max:255|unique:users',
                'identifier' => 'required|string|max:31|unique:users',
                'role'       => 'required|in:STUDENT,TEACHER,VP,ADMIN',
                'password'   => 'required|string|min:8|confirmed',
                'class_id'   => 'nullable|exists:classes,id',
            ]);

            if ($validated['role'] === 'STUDENT' && isset($validated['class_id'])) {
                $class = SchoolClass::find($validated['class_id']);
                if (!$class) {
                    return back()->withErrors('Selected class does not exist.')->withInput();
                }
                if ($class->isAtCapacity()) {
                    return back()->withErrors('Selected class has reached its maximum capacity.')->withInput();
                }
            }

            DB::beginTransaction();

            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'identifier' => $validated['identifier'],
                'role'       => $validated['role'],
                'password'   => bcrypt($validated['password']),
                'class_id'   => $validated['class_id'] ?? null,
            ]);

            if ($validated['role'] === 'STUDENT' && isset($validated['class_id'])) {
                $this->setStudentOrder($user);
                $this->autoEnrollStudentInActivities($user);
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        try {
            $classes = SchoolClass::where('capacity', '>', 0)
                ->orWhere('id', $user->class_id)
                ->get();

            $classActivities = collect();
            $enrolledActivityIds = [];
            if ($user->role === 'STUDENT' && $user->class_id) {
                $classActivities = Activity::where('class_id', $user->class_id)
                    ->where('deleted_at', null)
                    ->with('subject', 'teacher', 'period')
                    ->get();
                $enrolledActivityIds = $user->activitiesAsStudent()->pluck('activity_id')->toArray();
            }

            return view('admin.users.edit', compact('user', 'classes', 'classActivities', 'enrolledActivityIds'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'identifier'  => 'required|string|max:31|unique:users,identifier,' . $user->id,
                'role'        => 'required|in:STUDENT,TEACHER,VP,ADMIN',
                'class_id'    => 'nullable|exists:classes,id',
                'activity_ids' => 'nullable|array',
                'activity_ids.*' => 'exists:activities,id',
            ]);

            $classChanged = $user->class_id !== ($validated['class_id'] ?? null);
            if ($classChanged && $validated['role'] === 'STUDENT' && isset($validated['class_id'])) {
                $newClass = SchoolClass::find($validated['class_id']);
                if ($newClass && $newClass->isAtCapacity()) {
                    return back()->withErrors('Selected class has reached its maximum capacity.')->withInput();
                }
            }

            DB::beginTransaction();

            $user->update([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'identifier' => $validated['identifier'],
                'role'       => $validated['role'],
                'class_id'   => $validated['class_id'] ?? null,
            ]);

            if ($user->role === 'STUDENT') {
                if ($classChanged) {
                    $this->setStudentOrder($user);
                    $user->activitiesAsStudent()->detach();
                    if ($user->class_id) {
                        $this->autoEnrollStudentInActivities($user);
                    }
                } else {
                    if (isset($validated['activity_ids'])) {
                        $user->activitiesAsStudent()->sync($validated['activity_ids']);
                    } else {
                        $user->activitiesAsStudent()->detach();
                    }
                }
            } else {
                $user->activitiesAsStudent()->detach();
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->role === 'ADMIN' && User::where('role', 'ADMIN')->count() <= 1) {
                return redirect()->route('users.index')->withErrors('Cannot delete the last admin user.');
            }

            DB::beginTransaction();

            if ($user->role === 'TEACHER') {
                $user->activities()->delete();
            }

            $user->delete();

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting user: ' . $e->getMessage());
        }
    }

    public function restore(User $user)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $user->restore();

            return redirect()->route('users.index')->with('success', 'User restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring user: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring user: ' . $e->getMessage());
        }
    }

    private function autoEnrollStudentInActivities(User $user)
    {
        if (!$user->class_id || $user->role !== 'STUDENT') {
            return;
        }

        $activities = Activity::where('class_id', $user->class_id)
            ->whereNull('deleted_at')
            ->pluck('id');

        $user->activitiesAsStudent()->sync($activities);
    }

    private function setStudentOrder(User $user)
    {
        if ($user->role !== 'STUDENT' || !$user->class_id) {
            $user->student_order = null;
            $user->save();
            return;
        }

        $maxOrder = User::where('class_id', $user->class_id)
            ->where('role', 'STUDENT')
            ->max('student_order');

        $user->student_order = $maxOrder ? $maxOrder + 1 : 1;
        $user->save();
    }
}