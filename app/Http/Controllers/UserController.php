<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\SchoolClass;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'identifier' => 'required|string|max:31|unique:users',
            'role' => 'required|in:STUDENT,TEACHER,VP,ADMIN',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'identifier' => $request->identifier,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $classes = SchoolClass::all();
        
        return view('admin.users.edit', compact('user', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'identifier' => 'required|string|max:31|unique:users,identifier,' . $user->id,
            'role' => 'required|in:STUDENT,TEACHER,VP,ADMIN',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'identifier' => $request->identifier,
            'role' => $request->role,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index');
    }
}
