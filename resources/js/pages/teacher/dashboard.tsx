import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/react';

interface Schedule {
    id: number;
    start_time: string;
    end_time: string;
    room: string;
    school_class: {
        name: string;
        grade: string;
    };
    subject: {
        name: string;
        code: string;
    };
}

interface Class {
    id: number;
    name: string;
    grade: string;
    students: Array<{
        id: number;
        name: string;
        student_id: string;
    }>;
}

interface AttendanceRecord {
    id: number;
    date: string;
    status: string;
    student: {
        name: string;
        student_id: string;
    };
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
    student: {
        name: string;
        student_id: string;
    };
    from_date: string;
    to_date: string;
    type: string;
    status: string;
}

interface Props {
    today_schedule: Schedule[];
    teaching_classes: Class[];
    recent_attendance: AttendanceRecord[];
    pending_leave_requests: LeaveRequest[];
    [key: string]: unknown;
}

export default function TeacherDashboard({
    today_schedule,
    teaching_classes,
    recent_attendance,
    pending_leave_requests,
}: Props) {
    const formatTime = (timeString: string) => {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    };

    return (
        <AppShell>
            <Head title="Teacher Dashboard" />
            
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        👩‍🏫 Teacher Dashboard
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400">
                        Your teaching schedule and class management
                    </p>
                </div>

                {/* Today's Schedule */}
                <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div className="flex justify-between items-center mb-4">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            📅 Today's Schedule
                        </h2>
                        <Link href={route('schedules.index')}>
                            <Button variant="outline" size="sm">
                                View Full Schedule
                            </Button>
                        </Link>
                    </div>
                    <div className="grid gap-4">
                        {today_schedule.length > 0 ? (
                            today_schedule.map((schedule) => (
                                <div
                                    key={schedule.id}
                                    className="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800"
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
                                                Class {schedule.school_class.name} • {schedule.room}
                                            </p>
                                        </div>
                                    </div>
                                    <Link
                                        href={route('attendance.create', {
                                            schedule_id: schedule.id,
                                            date: new Date().toISOString().split('T')[0],
                                        })}
                                    >
                                        <Button size="sm">
                                            Take Attendance
                                        </Button>
                                    </Link>
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

                {/* Classes and Recent Activity */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Teaching Classes */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            🏫 My Classes
                        </h2>
                        <div className="space-y-3">
                            {teaching_classes.map((schoolClass) => (
                                <div
                                    key={schoolClass.id}
                                    className="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                >
                                    <div>
                                        <h3 className="font-medium text-gray-900 dark:text-gray-100">
                                            Class {schoolClass.name}
                                        </h3>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">
                                            Grade {schoolClass.grade} • {schoolClass.students.length} students
                                        </p>
                                    </div>
                                    <Link href={route('attendance.show', { type: 'class', id: schoolClass.id })}>
                                        <Button variant="outline" size="sm">
                                            View Reports
                                        </Button>
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>

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
                                            {record.student.name}
                                        </p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">
                                            {record.schedule.subject.name} • Class {record.schedule.school_class.name}
                                        </p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">
                                            {record.date}
                                        </p>
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
                </div>

                {/* Pending Leave Requests */}
                {pending_leave_requests.length > 0 && (
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                📝 Pending Leave Requests
                            </h2>
                            <Link href={route('leave-requests.index')}>
                                <Button variant="outline" size="sm">
                                    Review All
                                </Button>
                            </Link>
                        </div>
                        <div className="grid gap-4">
                            {pending_leave_requests.slice(0, 3).map((request) => (
                                <div
                                    key={request.id}
                                    className="flex justify-between items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800"
                                >
                                    <div>
                                        <h3 className="font-medium text-gray-900 dark:text-gray-100">
                                            {request.student.name}
                                        </h3>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            {request.student.student_id} • {request.type}
                                        </p>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">
                                            {request.from_date} to {request.to_date}
                                        </p>
                                    </div>
                                    <Link href={route('leave-requests.show', request.id)}>
                                        <Button size="sm">
                                            Review
                                        </Button>
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Quick Actions */}
                <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        ⚡ Quick Actions
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Link href={route('attendance.create')}>
                            <Button className="w-full" variant="outline">
                                📝 Take Attendance
                            </Button>
                        </Link>
                        <Link href={route('schedules.index')}>
                            <Button className="w-full" variant="outline">
                                📅 View Schedule
                            </Button>
                        </Link>
                        <Link href={route('leave-requests.index')}>
                            <Button className="w-full" variant="outline">
                                📋 Review Leaves
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </AppShell>
    );
}