import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/react';

interface Schedule {
    id: number;
    start_time: string;
    end_time: string;
    room: string;
    subject: {
        name: string;
        code: string;
    };
    teacher: {
        name: string;
    };
    school_class: {
        name: string;
    };
}

interface Class {
    id: number;
    name: string;
    grade: string;
    schedules: {
        subject: {
            name: string;
            code: string;
        };
        teacher: {
            name: string;
        };
    }[];
}

interface AttendanceStat {
    status: string;
    count: number;
    percentage: number;
}

interface AttendanceRecord {
    id: number;
    date: string;
    status: string;
    notes: string | null;
    schedule: {
        subject: {
            name: string;
        };
        school_class: {
            name: string;
        };
    };
}

interface LeaveRequest {
    id: number;
    from_date: string;
    to_date: string;
    type: string;
    status: string;
    reason: string;
    reviewer?: {
        name: string;
    };
}

interface Props {
    student_classes: Class[];
    today_schedule: Schedule[];
    attendance_stats: AttendanceStat[];
    recent_attendance: AttendanceRecord[];
    leave_requests: LeaveRequest[];
    [key: string]: unknown;
}

export default function StudentDashboard({
    student_classes,
    today_schedule,
    attendance_stats,
    recent_attendance,
    leave_requests,
}: Props) {
    const formatTime = (timeString: string) => {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    };

    const presentStat = attendance_stats.find(stat => stat.status === 'present');
    const overallAttendance = presentStat ? Math.round(presentStat.percentage) : 0;

    return (
        <AppShell>
            <Head title="Student Dashboard" />
            
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        👨‍🎓 Student Dashboard
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400">
                        Your personal attendance records and schedule
                    </p>
                </div>

                {/* Attendance Overview */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg p-6 text-white">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">✅</div>
                            <div>
                                <p className="text-sm font-medium opacity-90">Present</p>
                                <p className="text-2xl font-bold">
                                    {attendance_stats.find(stat => stat.status === 'present')?.count || 0}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-gradient-to-r from-red-500 to-rose-600 rounded-lg p-6 text-white">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">❌</div>
                            <div>
                                <p className="text-sm font-medium opacity-90">Absent</p>
                                <p className="text-2xl font-bold">
                                    {attendance_stats.find(stat => stat.status === 'absent')?.count || 0}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-gradient-to-r from-yellow-500 to-amber-600 rounded-lg p-6 text-white">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">⏰</div>
                            <div>
                                <p className="text-sm font-medium opacity-90">Late</p>
                                <p className="text-2xl font-bold">
                                    {attendance_stats.find(stat => stat.status === 'late')?.count || 0}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">📊</div>
                            <div>
                                <p className="text-sm font-medium opacity-90">Attendance Rate</p>
                                <p className="text-2xl font-bold">{overallAttendance}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Today's Schedule */}
                <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        📅 Today's Schedule
                    </h2>
                    <div className="grid gap-4">
                        {today_schedule.length > 0 ? (
                            today_schedule.map((schedule) => (
                                <div
                                    key={schedule.id}
                                    className="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-100 dark:border-blue-800"
                                >
                                    <div className="flex items-center space-x-4">
                                        <div className="text-center">
                                            <div className="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                {formatTime(schedule.start_time)}
                                            </div>
                                            <div className="text-xs text-gray-500 dark:text-gray-400">
                                                {formatTime(schedule.end_time)}
                                            </div>
                                        </div>
                                        <div>
                                            <h3 className="font-semibold text-gray-900 dark:text-gray-100">
                                                {schedule.subject.name}
                                            </h3>
                                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                                {schedule.teacher.name} • {schedule.room}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Class {schedule.school_class.name}
                                        </p>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-8">
                                <div className="text-4xl mb-2">🎉</div>
                                <p className="text-gray-500 dark:text-gray-400">
                                    No classes scheduled for today
                                </p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Recent Attendance and Leave Requests */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Recent Attendance */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                📋 Recent Attendance
                            </h2>
                            <Link href={route('attendance.index')}>
                                <Button variant="outline" size="sm">
                                    View All
                                </Button>
                            </Link>
                        </div>
                        <div className="space-y-3">
                            {recent_attendance.slice(0, 5).map((record) => (
                                <div
                                    key={record.id}
                                    className="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                >
                                    <div>
                                        <p className="font-medium text-sm text-gray-900 dark:text-gray-100">
                                            {record.schedule.subject.name}
                                        </p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">
                                            Class {record.schedule.school_class.name} • {record.date}
                                        </p>
                                        {record.notes && (
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {record.notes}
                                            </p>
                                        )}
                                    </div>
                                    <span
                                        className={`px-2 py-1 text-xs rounded-full ${
                                            record.status === 'present'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                : record.status === 'late'
                                                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                                : record.status === 'excused'
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                        }`}
                                    >
                                        {record.status}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Leave Requests */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                📝 My Leave Requests
                            </h2>
                            <Link href={route('leave-requests.create')}>
                                <Button size="sm">
                                    Request Leave
                                </Button>
                            </Link>
                        </div>
                        <div className="space-y-3">
                            {leave_requests.length > 0 ? (
                                leave_requests.slice(0, 4).map((request) => (
                                    <div
                                        key={request.id}
                                        className="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                    >
                                        <div className="flex justify-between items-start mb-2">
                                            <p className="font-medium text-sm text-gray-900 dark:text-gray-100">
                                                {request.type} Leave
                                            </p>
                                            <span
                                                className={`px-2 py-1 text-xs rounded-full ${
                                                    request.status === 'approved'
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                        : request.status === 'pending'
                                                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                                }`}
                                            >
                                                {request.status}
                                            </span>
                                        </div>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">
                                            {request.from_date} to {request.to_date}
                                        </p>
                                        <p className="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">
                                            {request.reason}
                                        </p>
                                    </div>
                                ))
                            ) : (
                                <p className="text-gray-500 dark:text-gray-400 text-center py-4">
                                    No leave requests submitted
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* My Classes */}
                <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        🏫 My Classes
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {student_classes.map((studentClass) => (
                            <div
                                key={studentClass.id}
                                className="p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border border-purple-100 dark:border-purple-800"
                            >
                                <h3 className="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Class {studentClass.name}
                                </h3>
                                <p className="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    Grade {studentClass.grade}
                                </p>
                                <div className="space-y-1">
                                    {studentClass.schedules.slice(0, 3).map((schedule, index) => (
                                        <div key={index} className="text-xs text-gray-500 dark:text-gray-400">
                                            {schedule.subject.name} - {schedule.teacher.name}
                                        </div>
                                    ))}
                                    {studentClass.schedules.length > 3 && (
                                        <div className="text-xs text-gray-400 dark:text-gray-500">
                                            +{studentClass.schedules.length - 3} more subjects
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AppShell>
    );
}