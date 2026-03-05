<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = Grade::query();
            
            if ($showDeleted) {
                $grades = $query->onlyTrashed()->paginate(100);
            } else {
                $grades = $query->paginate(100);
            }
            
            return view('grades.index', compact('grades', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading grades: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading grades: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('grades.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|unique:grades,id',
            ], [
                'id.required' => 'Grade level is required.',
                'id.integer' => 'Grade level must be a number.',
                'id.unique' => 'This grade level already exists.',
            ]);

            DB::beginTransaction();

            Grade::create($validated);

            DB::commit();

            return redirect()->route('grades.index')->with('success', 'Grade created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating grade: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating grade: ' . $e->getMessage());
        }
    }

    public function edit(Grade $grade)
    {
        return view('grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|unique:grades,id,' . $grade->id . ',id',
            ], [
                'id.required' => 'Grade level is required.',
                'id.integer' => 'Grade level must be a number.',
                'id.unique' => 'This grade level already exists.',
            ]);

            DB::beginTransaction();

            $grade->update($validated);

            DB::commit();

            return redirect()->route('grades.index')->with('success', 'Grade updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating grade: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating grade: ' . $e->getMessage());
        }
    }

    public function destroy(Grade $grade)
    {
        try {
            DB::beginTransaction();

            $grade->delete();

            DB::commit();

            return redirect()->route('grades.index')->with('success', 'Grade deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting grade: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting grade: ' . $e->getMessage());
        }
    }

    public function forceDestroy(Grade $grade)
    {
        try {
            if (!in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            DB::beginTransaction();

            $grade->forceDelete();

            DB::commit();

            return redirect()->route('grades.index')->with('success', 'Grade permanently deleted. All related classes, activities, and other records have also been deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting grade: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting grade: ' . $e->getMessage());
        }
    }

    public function restore(Grade $grade)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $grade->restore();

            return redirect()->route('grades.index')->with('success', 'Grade restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring grade: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring grade: ' . $e->getMessage());
        }
    }
}
