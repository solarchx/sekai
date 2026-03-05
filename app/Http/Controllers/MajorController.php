<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MajorController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $showDeleted = $request->has('show_deleted') && auth()->user()->role === 'ADMIN';
            
            $query = Major::query();
            
            if ($showDeleted) {
                $majors = $query->onlyTrashed()->paginate(100);
            } else {
                $majors = $query->paginate(100);
            }
            
            return view('admin.majors.index', compact('majors', 'showDeleted'));
        } catch (\Exception $e) {
            Log::error('Error loading majors: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error loading majors: ' . $e->getMessage());
        }
    }

    
    public function create()
    {
        return view('admin.majors.create');
    }

    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:majors,name',
            ], [
                'name.required' => 'Major name is required.',
                'name.unique' => 'This major name already exists.',
            ]);

            DB::beginTransaction();

            Major::create($validated);

            DB::commit();

            return redirect()->route('majors.index')->with('success', 'Major created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating major: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error creating major: ' . $e->getMessage());
        }
    }

    
    public function edit(Major $major)
    {
        return view('admin.majors.edit', compact('major'));
    }

    
    public function update(Request $request, Major $major)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:majors,name,' . $major->id,
            ], [
                'name.required' => 'Major name is required.',
                'name.unique' => 'This major name already exists.',
            ]);

            DB::beginTransaction();

            $major->update($validated);

            DB::commit();

            return redirect()->route('majors.index')->with('success', 'Major updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating major: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error updating major: ' . $e->getMessage());
        }
    }

    
    public function destroy(Major $major)
    {
        try {
            DB::beginTransaction();

            $major->delete();

            DB::commit();

            return redirect()->route('majors.index')->with('success', 'Major deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting major: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting major: ' . $e->getMessage());
        }
    }

    public function forceDestroy(Major $major)
    {
        try {
            if (!in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $major->forceDelete();

            return redirect()->route('majors.index')->with('success', 'Major permanently deleted.');
        } catch (\Exception $e) {
            Log::error('Error force deleting major: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error deleting major: ' . $e->getMessage());
        }
    }
    
    public function restore(Major $major)
    {
        try {
            if (auth()->user()->role !== 'ADMIN') {
                return redirect()->back()->withErrors('Unauthorized action.');
            }

            $major->restore();

            return redirect()->route('majors.index')->with('success', 'Major restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring major: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error restoring major: ' . $e->getMessage());
        }
    }
}
