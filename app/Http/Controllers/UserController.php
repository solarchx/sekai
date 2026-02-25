<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
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
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'identifier' => 'required|string|max:31|unique:users',
                'role' => 'required|in:STUDENT,TEACHER,VP,ADMIN',
                'password' => 'required|string|min:8|confirmed',
                'class_id' => 'nullable|exists:classes,id',
            ], [
                'name.required' => 'Name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be a valid email address.',
                'email.unique' => 'This email is already registered.',
                'identifier.required' => 'Identifier is required.',
                'identifier.max' => 'Identifier must not exceed 31 characters.',
                'identifier.unique' => 'This identifier is already taken.',
                'role.required' => 'Role is required.',
                'role.in' => 'Role must be STUDENT, TEACHER, VP, or ADMIN.',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Passwords do not match.',
                'class_id.exists' => 'Selected class does not exist.',
            ]);

            // Check class capacity for students
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
                'name' => $validated['name'],
                'email' => $validated['email'],
                'identifier' => $validated['identifier'],
                'role' => $validated['role'],
                'password' => bcrypt($validated['password']),
                'class_id' => $validated['class_id'] ?? null,
            ]);

            // If student is assigned to a class, auto-enroll in class activities
            if ($validated['role'] === 'STUDENT' && isset($validated['class_id'])) {
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            $classes = SchoolClass::where('capacity', '>', 0)
                ->orWhere('id', $user->class_id)
                ->get();
            
            return view('admin.users.edit', compact('user', 'classes'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'identifier' => 'required|string|max:31|unique:users,identifier,' . $user->id,
                'role' => 'required|in:STUDENT,TEACHER,VP,ADMIN',
                'class_id' => 'nullable|exists:classes,id',
            ], [
                'name.required' => 'Name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Email must be a valid email address.',
                'email.unique' => 'This email is already registered.',
                'identifier.required' => 'Identifier is required.',
                'identifier.max' => 'Identifier must not exceed 31 characters.',
                'identifier.unique' => 'This identifier is already taken.',
                'role.required' => 'Role is required.',
                'role.in' => 'Role must be STUDENT, TEACHER, VP, or ADMIN.',
                'class_id.exists' => 'Selected class does not exist.',
            ]);

            // If changing class assignment, check capacity
            $classChanged = $user->class_id !== ($validated['class_id'] ?? null);
            if ($classChanged && $validated['role'] === 'STUDENT' && isset($validated['class_id'])) {
                $newClass = SchoolClass::find($validated['class_id']);
                
                if ($newClass && $newClass->isAtCapacity()) {
                    return back()->withErrors('Selected class has reached its maximum capacity.')->withInput();
                }
            }

            DB::beginTransaction();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'identifier' => $validated['identifier'],
                'role' => $validated['role'],
                'class_id' => $validated['class_id'] ?? null,
            ]);

            // If class was changed and user is student, update activity enrollments
            if ($classChanged && $user->role === 'STUDENT') {
                // Remove from old class activities (if applicable)
                if ($user->class_id) {
                    $user->activities()->detach();
                }
                
                // Auto-enroll in new class activities
                if (isset($validated['class_id'])) {
                    $this->autoEnrollStudentInActivities($user);
                }
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting admins
            if ($user->role === 'ADMIN' && User::where('role', 'ADMIN')->count() <= 1) {
                return redirect()->route('users.index')->withErrors('Cannot delete the last admin user.');
            }

            DB::beginTransaction();

            // Soft delete activities associated with user if teacher
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

    /**
     * Restore a soft-deleted user (admin only).
     */
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

    /**
     * Auto-enroll student in all activities of their assigned class.
     */
    private function autoEnrollStudentInActivities(User $user)
    {
        if (!$user->class_id || $user->role !== 'STUDENT') {
            return;
        }

        $activities = $user->schoolClass->activities()->whereNull('deleted_at')->get();

        foreach ($activities as $activity) {
            if ($user->activities()->where('activity_id', $activity->id)->exists()) {
                continue;
            }

            $maxOrder = DB::table('activity_students')
                ->where('activity_id', $activity->id)
                ->max('student_order') ?? 0;

            $user->activities()->attach($activity->id, ['student_order' => $maxOrder + 1]);
        }
    }
}
