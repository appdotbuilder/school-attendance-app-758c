<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $query = User::query();
        
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(15);
        
        return Inertia::render('admin/users/index', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $classes = SchoolClass::active()->get();
        
        return Inertia::render('admin/users/create', [
            'classes' => $classes,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|string|unique:users,student_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_id' => $validated['student_id'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);
        
        // Attach classes if user is a student
        if ($validated['role'] === 'student' && isset($validated['class_ids'])) {
            $user->classes()->attach($validated['class_ids'], [
                'enrolled_at' => now(),
                'is_active' => true,
            ]);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $user->load(['classes', 'teachingSchedules.subject', 'teachingSchedules.schoolClass']);
        
        return Inertia::render('admin/users/show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $user->load('classes');
        $classes = SchoolClass::active()->get();
        
        return Inertia::render('admin/users/edit', [
            'user' => $user,
            'classes' => $classes,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,student',
            'student_id' => 'nullable|string|unique:users,student_id,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
        ]);
        
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_id' => $validated['student_id'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ];
        
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }
        
        $user->update($updateData);
        
        // Update class associations for students
        if ($validated['role'] === 'student') {
            if (isset($validated['class_ids'])) {
                $user->classes()->sync(
                    collect($validated['class_ids'])->mapWithKeys(function ($classId) {
                        return [$classId => [
                            'enrolled_at' => now(),
                            'is_active' => true,
                        ]];
                    })
                );
            } else {
                $user->classes()->detach();
            }
        }
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        // Prevent deleting the last admin
        if ($user->isAdmin() && User::admins()->count() <= 1) {
            return back()->withErrors(['error' => 'Cannot delete the last admin user.']);
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}