<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isTeacher()) {
            return $this->teacherDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    /**
     * Admin dashboard with overall statistics
     */
    protected function adminDashboard()
    {
        $totalStudents = User::students()->count();
        $totalTeachers = User::teachers()->count();
        $totalClasses = SchoolClass::active()->count();
        $totalSubjects = Subject::active()->count();
        
        // Today's attendance statistics
        $today = now()->format('Y-m-d');
        $todayAttendance = Attendance::whereDate('date', $today)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        $attendanceStats = [
            'present' => $todayAttendance->where('status', 'present')->first()->count ?? 0,
            'absent' => $todayAttendance->where('status', 'absent')->first()->count ?? 0,
            'late' => $todayAttendance->where('status', 'late')->first()->count ?? 0,
            'excused' => $todayAttendance->where('status', 'excused')->first()->count ?? 0,
        ];
        
        // Recent leave requests
        $pendingLeaveRequests = LeaveRequest::with('student')
            ->pending()
            ->latest()
            ->take(5)
            ->get();
        
        // Weekly attendance trends
        $weeklyStats = Attendance::whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->select(
                DB::raw('DATE(date) as date'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();
        
        return Inertia::render('admin/dashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'total_classes' => $totalClasses,
                'total_subjects' => $totalSubjects,
            ],
            'attendance_stats' => $attendanceStats,
            'pending_leave_requests' => $pendingLeaveRequests,
            'weekly_stats' => $weeklyStats,
        ]);
    }

    /**
     * Teacher dashboard with daily schedule and class management
     */
    protected function teacherDashboard()
    {
        $teacher = auth()->user();
        $today = strtolower(now()->format('l')); // monday, tuesday, etc.
        
        // Today's teaching schedule
        $todaySchedule = Schedule::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
        
        // Classes taught by this teacher
        $teachingClasses = Schedule::with(['schoolClass.students'])
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->select('class_id')
            ->distinct()
            ->get()
            ->pluck('schoolClass')
            ->unique('id');
        
        // Recent attendance records by this teacher
        $recentAttendance = Attendance::with(['student', 'schedule.subject', 'schedule.schoolClass'])
            ->where('recorded_by', $teacher->id)
            ->latest()
            ->take(10)
            ->get();
        
        // Pending leave requests for students in teacher's classes
        $studentIds = $teachingClasses->flatMap->students->pluck('id');
        $pendingLeaveRequests = LeaveRequest::with('student')
            ->whereIn('student_id', $studentIds)
            ->pending()
            ->latest()
            ->take(5)
            ->get();
        
        return Inertia::render('teacher/dashboard', [
            'today_schedule' => $todaySchedule,
            'teaching_classes' => $teachingClasses,
            'recent_attendance' => $recentAttendance,
            'pending_leave_requests' => $pendingLeaveRequests,
        ]);
    }

    /**
     * Student dashboard with personal attendance and schedule
     */
    protected function studentDashboard()
    {
        $student = auth()->user();
        
        // Student's classes and schedules
        $studentClasses = $student->classes()->with(['schedules.subject', 'schedules.teacher'])->get();
        
        // Today's schedule
        $today = strtolower(now()->format('l'));
        $todaySchedule = Schedule::with(['subject', 'teacher', 'schoolClass'])
            ->whereHas('schoolClass.students', function($query) use ($student) {
                $query->where('users.id', $student->id);
            })
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
        
        // Personal attendance statistics
        $attendanceStats = Attendance::where('student_id', $student->id)
            ->selectRaw('
                status,
                COUNT(*) as count,
                COUNT(*) * 100.0 / (SELECT COUNT(*) FROM attendance WHERE student_id = ?) as percentage
            ', [$student->id])
            ->groupBy('status')
            ->get();
        
        // Recent attendance records
        $recentAttendance = Attendance::with(['schedule.subject', 'schedule.schoolClass'])
            ->where('student_id', $student->id)
            ->latest()
            ->take(10)
            ->get();
        
        // Leave requests
        $leaveRequests = LeaveRequest::with('reviewer')
            ->where('student_id', $student->id)
            ->latest()
            ->take(5)
            ->get();
        
        return Inertia::render('student/dashboard', [
            'student_classes' => $studentClasses,
            'today_schedule' => $todaySchedule,
            'attendance_stats' => $attendanceStats,
            'recent_attendance' => $recentAttendance,
            'leave_requests' => $leaveRequests,
        ]);
    }
}