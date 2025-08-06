<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return $this->adminIndex($request);
        } elseif ($user->isTeacher()) {
            return $this->teacherIndex($request);
        } else {
            return $this->studentIndex($request);
        }
    }

    /**
     * Admin view of all attendance records
     */
    protected function adminIndex(Request $request)
    {
        $query = Attendance::with(['student', 'schedule.subject', 'schedule.schoolClass', 'recordedBy']);
        
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $attendance = $query->latest()->paginate(20);
        $classes = SchoolClass::active()->get();
        
        return Inertia::render('admin/attendance/index', [
            'attendance' => $attendance,
            'classes' => $classes,
            'filters' => $request->only(['class_id', 'date', 'status']),
        ]);
    }

    /**
     * Teacher view of attendance for their classes
     */
    protected function teacherIndex(Request $request)
    {
        $teacher = auth()->user();
        
        $query = Attendance::with(['student', 'schedule.subject', 'schedule.schoolClass'])
            ->whereHas('schedule', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            });
        
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }
        
        $attendance = $query->latest()->paginate(20);
        
        // Get classes taught by this teacher
        $classIds = Schedule::where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('class_id');
        
        $classes = SchoolClass::whereIn('id', $classIds)->get();
        
        return Inertia::render('teacher/attendance/index', [
            'attendance' => $attendance,
            'classes' => $classes,
            'filters' => $request->only(['class_id', 'date']),
        ]);
    }

    /**
     * Student view of their own attendance
     */
    protected function studentIndex(Request $request)
    {
        $student = auth()->user();
        
        $query = Attendance::with(['schedule.subject', 'schedule.schoolClass', 'recordedBy'])
            ->where('student_id', $student->id);
        
        if ($request->has('subject_id') && $request->subject_id) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        
        $attendance = $query->latest()->paginate(20);
        
        // Get subjects for this student
        $classIds = $student->classes()->pluck('classes.id');
        $subjects = Subject::whereHas('schedules', function ($query) use ($classIds) {
            $query->whereIn('class_id', $classIds);
        })->get();
        
        return Inertia::render('student/attendance/index', [
            'attendance' => $attendance,
            'subjects' => $subjects,
            'filters' => $request->only(['subject_id', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show the form for recording attendance.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isTeacher() && !$user->isAdmin()) {
            abort(403, 'Only teachers and admins can record attendance.');
        }
        
        $scheduleId = $request->get('schedule_id');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        if (!$scheduleId) {
            // Show schedule selection for teacher
            $schedules = Schedule::with(['schoolClass', 'subject'])
                ->when($user->isTeacher(), function($q) use ($user) {
                    $q->where('teacher_id', $user->id);
                })
                ->where('is_active', true)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
            
            return Inertia::render('attendance/select-schedule', [
                'schedules' => $schedules,
                'date' => $date,
            ]);
        }
        
        $schedule = Schedule::with(['schoolClass.students', 'subject'])
            ->findOrFail($scheduleId);
        
        // Check if teacher is authorized for this schedule
        if ($user->isTeacher() && $schedule->teacher_id !== $user->id) {
            abort(403, 'You can only record attendance for your own classes.');
        }
        
        // Get existing attendance for this date
        $existingAttendance = Attendance::where('schedule_id', $scheduleId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');
        
        $students = $schedule->schoolClass->students->map(function($student) use ($existingAttendance) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'student_id' => $student->student_id,
                'status' => $existingAttendance->get($student->id)->status ?? 'absent',
                'notes' => $existingAttendance->get($student->id)->notes ?? '',
            ];
        });
        
        return Inertia::render('attendance/create', [
            'schedule' => $schedule,
            'students' => $students,
            'date' => $date,
        ]);
    }

    /**
     * Store attendance records.
     */
    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        
        foreach ($validated['attendance'] as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'schedule_id' => $validated['schedule_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                    'recorded_by' => $user->id,
                ]
            );
        }
        
        return redirect()->route('attendance.index')
            ->with('success', 'Attendance recorded successfully.');
    }

    /**
     * Display attendance report for a specific class or student.
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isTeacher()) {
            abort(403);
        }
        
        $type = $request->get('type', 'class'); // class or student
        $id = $request->get('id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        
        if ($type === 'class') {
            $class = SchoolClass::with(['students', 'schedules.subject'])->findOrFail($id);
            
            // Get attendance data for all students in the class
            $attendanceData = Attendance::with(['student', 'schedule.subject'])
                ->whereHas('schedule', function($q) use ($id) {
                    $q->where('class_id', $id);
                })
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->get()
                ->groupBy(['student_id', 'schedule.subject.name']);
            
            return Inertia::render('admin/reports/class-attendance', [
                'class' => $class,
                'attendance_data' => $attendanceData,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]);
        } else {
            $student = User::students()->findOrFail($id);
            
            $attendanceData = Attendance::with(['schedule.subject', 'schedule.schoolClass'])
                ->where('student_id', $id)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->get()
                ->groupBy('schedule.subject.name');
            
            return Inertia::render('admin/reports/student-attendance', [
                'student' => $student,
                'attendance_data' => $attendanceData,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]);
        }
    }
}