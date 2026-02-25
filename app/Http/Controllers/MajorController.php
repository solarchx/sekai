<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.majors.create');
    }

    /**\n     * Store a newly created resource in storage.
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Major $major)
    {
        return view('admin.majors.edit', compact('major'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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

    /**
     * Restore a soft-deleted major (admin only).
     */
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
