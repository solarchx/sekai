<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Grade;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = Subject::query();
            
            if ($showDeleted) {
                $subjects = $query->onlyTrashed()->paginate(100);
            } else {
                $subjects = $query->paginate(100);
            }
            
            return view('subjects.index', compact('subjects', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading subjects: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading subjects: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $majors = Major::all();
        $grades = Grade::all();
        return view('subjects.create', compact('majors', 'grades'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:subjects,name',
                'majors' => 'nullable|array',
                'majors.*' => 'exists:majors,id',
                'grades' => 'nullable|array',
                'grades.*' => 'exists:grades,id',
            ], [
                'name.required' => 'Subject name is required.',
                'name.unique' => 'This subject name already exists.',
            ]);

            DB::beginTransaction();

            $subject = Subject::create(['name' => $validated['name']]);

            // Attach selected majors and grades
            if (!empty($validated['majors'])) {
                $subject->majors()->sync($validated['majors']);
            }
            if (!empty($validated['grades'])) {
                $subject->grades()->sync($validated['grades']);
            }

            DB::commit();

            return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating subject: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating subject: ' . $e->getMessage());
        }
    }

    public function edit(Subject $subject)
    {
        $majors = Major::all();
        $grades = Grade::all();
        // Load existing relationships for preselection
        $subject->load('majors', 'grades');
        return view('subjects.edit', compact('subject', 'majors', 'grades'));
    }

    public function update(Request $request, Subject $subject)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
                'majors' => 'nullable|array',
                'majors.*' => 'exists:majors,id',
                'grades' => 'nullable|array',
                'grades.*' => 'exists:grades,id',
            ], [
                'name.required' => 'Subject name is required.',
                'name.unique' => 'This subject name already exists.',
            ]);

            DB::beginTransaction();

            $subject->update(['name' => $validated['name']]);

            // Sync majors and grades
            $subject->majors()->sync($validated['majors'] ?? []);
            $subject->grades()->sync($validated['grades'] ?? []);

            DB::commit();

            return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating subject: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating subject: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            DB::beginTransaction();

            $subject->delete();

            DB::commit();

            return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting subject: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting subject: ' . $e->getMessage());
        }
    }

    public function restore(Subject $subject)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $subject->restore();

            return redirect()->route('subjects.index')->with('success', 'Subject restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring subject: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring subject: ' . $e->getMessage());
        }
    }
}
