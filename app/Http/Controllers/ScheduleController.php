<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isTeacher()) {
            return $this->teacherSchedules($request);
        } else {
            return $this->adminSchedules($request);
        }
    }

    /**
     * Teacher's schedule view
     */
    protected function teacherSchedules(Request $request)
    {
        $teacher = auth()->user();
        
        $schedules = Schedule::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        
        return Inertia::render('teacher/schedules/index', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Admin schedule management view
     */
    protected function adminSchedules(Request $request)
    {
        $query = Schedule::with(['schoolClass', 'subject', 'teacher']);
        
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->has('teacher_id') && $request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        if ($request->has('day_of_week') && $request->day_of_week) {
            $query->where('day_of_week', $request->day_of_week);
        }
        
        $schedules = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(20);
        
        $classes = SchoolClass::active()->get();
        $teachers = User::teachers()->get();
        $subjects = Subject::active()->get();
        
        return Inertia::render('admin/schedules/index', [
            'schedules' => $schedules,
            'classes' => $classes,
            'teachers' => $teachers,
            'subjects' => $subjects,
            'filters' => $request->only(['class_id', 'teacher_id', 'day_of_week']),
        ]);
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $classes = SchoolClass::active()->get();
        $subjects = Subject::active()->get();
        $teachers = User::teachers()->get();
        
        return Inertia::render('admin/schedules/create', [
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
        ]);
        
        // Check for conflicts
        $conflict = Schedule::where('teacher_id', $validated['teacher_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('is_active', true)
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();
        
        if ($conflict) {
            return back()->withErrors(['teacher_id' => 'Teacher has a conflicting schedule at this time.']);
        }
        
        Schedule::create($validated);
        
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule created successfully.');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['schoolClass.students', 'subject', 'teacher']);
        
        return Inertia::render('schedules/show', [
            'schedule' => $schedule,
        ]);
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(Schedule $schedule)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $classes = SchoolClass::active()->get();
        $subjects = Subject::active()->get();
        $teachers = User::teachers()->get();
        
        return Inertia::render('admin/schedules/edit', [
            'schedule' => $schedule,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);
        
        // Check for conflicts (excluding current schedule)
        $conflict = Schedule::where('teacher_id', $validated['teacher_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('is_active', true)
            ->where('id', '!=', $schedule->id)
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();
        
        if ($conflict) {
            return back()->withErrors(['teacher_id' => 'Teacher has a conflicting schedule at this time.']);
        }
        
        $schedule->update($validated);
        
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule updated successfully.');
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        
        $schedule->delete();
        
        return redirect()->route('schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}