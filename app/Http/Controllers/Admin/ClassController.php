<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $query = SchoolClass::withCount('students');
        
        if ($request->has('grade') && $request->grade) {
            $query->where('grade', $request->grade);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('grade', 'like', "%{$search}%");
            });
        }
        
        $classes = $query->latest()->paginate(15);
        
        return Inertia::render('admin/classes/index', [
            'classes' => $classes,
            'filters' => $request->only(['grade', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new class.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        return Inertia::render('admin/classes/create');
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'grade' => 'required|string|max:10',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        
        SchoolClass::create($validated);
        
        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified class.
     */
    public function show(SchoolClass $class)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $class->load(['students', 'schedules.subject', 'schedules.teacher']);
        
        return Inertia::render('admin/classes/show', [
            'class' => $class,
        ]);
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit(SchoolClass $class)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $class->load('students');
        $availableStudents = User::students()
            ->whereDoesntHave('classes', function($query) use ($class) {
                $query->where('classes.id', $class->id);
            })
            ->get();
        
        return Inertia::render('admin/classes/edit', [
            'class' => $class,
            'available_students' => $availableStudents,
        ]);
    }

    /**
     * Update the specified class.
     */
    public function update(Request $request, SchoolClass $class)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id,
            'grade' => 'required|string|max:10',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
        ]);
        
        $class->update([
            'name' => $validated['name'],
            'grade' => $validated['grade'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        // Update student associations
        if (isset($validated['student_ids'])) {
            $class->students()->sync(
                collect($validated['student_ids'])->mapWithKeys(function ($studentId) {
                    return [$studentId => [
                        'enrolled_at' => now(),
                        'is_active' => true,
                    ]];
                })
            );
        }
        
        return redirect()->route('admin.classes.show', $class)
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified class.
     */
    public function destroy(SchoolClass $class)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        // Check if class has active schedules or students
        if ($class->schedules()->count() > 0 || $class->students()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete class with active schedules or enrolled students.']);
        }
        
        $class->delete();
        
        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}