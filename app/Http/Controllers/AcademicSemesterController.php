<?php

namespace App\Http\Controllers;

use App\Models\AcademicSemester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicSemesterController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = AcademicSemester::query();
            
            if ($showDeleted) {
                $semesters = $query->onlyTrashed()->paginate(100);
            } else {
                $semesters = $query->paginate(100);
            }
            
            return view('semesters.index', compact('semesters', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading semesters: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading semesters: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('semesters.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'academic_year' => 'required|string|max:9',
                'semester' => 'required|integer|between:1,2',
            ], [
                'academic_year.required' => 'Academic year is required.',
                'academic_year.max' => 'Academic year must not exceed 9 characters.',
                'semester.required' => 'Semester is required.',
                'semester.between' => 'Semester must be either 1 or 2.',
            ]);

            
            $exists = AcademicSemester::where('academic_year', $validated['academic_year'])
                ->where('semester', $validated['semester'])
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['semester' => 'This semester already exists.'])->withInput();
            }

            DB::beginTransaction();

            AcademicSemester::create($validated);

            DB::commit();

            return redirect()->route('semesters.index')->with('success', 'Semester created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating semester: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating semester: ' . $e->getMessage());
        }
    }

    public function edit(AcademicSemester $semester)
    {
        return view('semesters.edit', compact('semester'));
    }

    public function update(Request $request, AcademicSemester $semester)
    {
        try {
            $validated = $request->validate([
                'academic_year' => 'required|string|max:9',
                'semester' => 'required|integer|between:1,2',
            ], [
                'academic_year.required' => 'Academic year is required.',
                'academic_year.max' => 'Academic year must not exceed 9 characters.',
                'semester.required' => 'Semester is required.',
                'semester.between' => 'Semester must be either 1 or 2.',
            ]);

            
            $exists = AcademicSemester::where('academic_year', $validated['academic_year'])
                ->where('semester', $validated['semester'])
                ->where('id', '!=', $semester->id)
                ->where('deleted_at', null)
                ->exists();

            if ($exists) {
                return back()->withErrors(['semester' => 'This semester already exists.'])->withInput();
            }

            DB::beginTransaction();

            $semester->update($validated);

            DB::commit();

            return redirect()->route('semesters.index')->with('success', 'Semester updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating semester: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating semester: ' . $e->getMessage());
        }
    }

    public function destroy(AcademicSemester $semester)
    {
        try {
            DB::beginTransaction();

            $semester->delete();

            DB::commit();

            return redirect()->route('semesters.index')->with('success', 'Semester deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting semester: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting semester: ' . $e->getMessage());
        }
    }

    public function forceDestroy(AcademicSemester $semester)
    {
        try {
            if (!in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            DB::beginTransaction();

            $semester->forceDelete();

            DB::commit();

            return redirect()->route('semesters.index')->with('success', 'Semester permanently deleted. All related lesson periods and activities have also been deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting semester: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting semester: ' . $e->getMessage());
        }
    }

    public function restore(AcademicSemester $semester)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $semester->restore();

            return redirect()->route('semesters.index')->with('success', 'Semester restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring semester: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring semester: ' . $e->getMessage());
        }
    }
}
