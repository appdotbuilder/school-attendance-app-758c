<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $query = Subject::withCount('schedules');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $subjects = $query->latest()->paginate(15);
        
        return Inertia::render('admin/subjects/index', [
            'subjects' => $subjects,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        return Inertia::render('admin/subjects/create');
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:subjects,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        
        Subject::create($validated);
        
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $subject->load(['schedules.schoolClass', 'schedules.teacher']);
        
        return Inertia::render('admin/subjects/show', [
            'subject' => $subject,
        ]);
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        return Inertia::render('admin/subjects/edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        
        $subject->update($validated);
        
        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        // Check if subject has active schedules
        if ($subject->schedules()->where('is_active', true)->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete subject with active schedules.']);
        }
        
        $subject->delete();
        
        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}