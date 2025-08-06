import React from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/react';

interface AttendanceStats {
    present: number;
    absent: number;
    late: number;
    excused: number;
}

interface Stats {
    total_students: number;
    total_teachers: number;
    total_classes: number;
    total_subjects: number;
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
    created_at: string;
}

interface Props {
    stats: Stats;
    attendance_stats: AttendanceStats;
    pending_leave_requests: LeaveRequest[];
    weekly_stats: Array<{
        date: string;
        status: string;
        count: number;
    }>;
    [key: string]: unknown;
}

export default function AdminDashboard({ stats, attendance_stats, pending_leave_requests }: Props) {
    const totalAttendance = attendance_stats.present + attendance_stats.absent + attendance_stats.late + attendance_stats.excused;
    const attendanceRate = totalAttendance > 0 ? Math.round((attendance_stats.present / totalAttendance) * 100) : 0;

    return (
        <AppShell>
            <Head title="Admin Dashboard" />
            
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        📊 Admin Dashboard
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400">
                        Overview of school attendance and management
                    </p>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">👨‍🎓</div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-gray-100">{stats.total_students}</p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">👩‍🏫</div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total Teachers</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-gray-100">{stats.total_teachers}</p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">🏫</div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total Classes</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-gray-100">{stats.total_classes}</p>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex items-center">
                            <div className="text-2xl mr-3">📚</div>
                            <div>
                                <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total Subjects</p>
                                <p className="text-2xl font-bold text-gray-900 dark:text-gray-100">{stats.total_subjects}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Today's Attendance */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            📈 Today's Attendance
                        </h2>
                        <div className="space-y-3">
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-gray-600 dark:text-gray-400">Present</span>
                                <span className="font-semibold text-green-600 dark:text-green-400">
                                    {attendance_stats.present}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-gray-600 dark:text-gray-400">Absent</span>
                                <span className="font-semibold text-red-600 dark:text-red-400">
                                    {attendance_stats.absent}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-gray-600 dark:text-gray-400">Late</span>
                                <span className="font-semibold text-yellow-600 dark:text-yellow-400">
                                    {attendance_stats.late}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-sm text-gray-600 dark:text-gray-400">Excused</span>
                                <span className="font-semibold text-blue-600 dark:text-blue-400">
                                    {attendance_stats.excused}
                                </span>
                            </div>
                            <div className="border-t dark:border-gray-600 pt-3 mt-4">
                                <div className="flex justify-between items-center">
                                    <span className="font-medium text-gray-900 dark:text-gray-100">Attendance Rate</span>
                                    <span className="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                        {attendanceRate}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Pending Leave Requests */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                📝 Pending Leave Requests
                            </h2>
                            <Link href={route('leave-requests.index')}>
                                <Button variant="outline" size="sm">
                                    View All
                                </Button>
                            </Link>
                        </div>
                        <div className="space-y-3">
                            {pending_leave_requests.length > 0 ? (
                                pending_leave_requests.slice(0, 4).map((request) => (
                                    <div key={request.id} className="flex justify-between items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <p className="font-medium text-sm text-gray-900 dark:text-gray-100">
                                                {request.student.name}
                                            </p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {request.student.student_id} • {request.type}
                                            </p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {request.from_date} to {request.to_date}
                                            </p>
                                        </div>
                                        <span className="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded-full">
                                            Pending
                                        </span>
                                    </div>
                                ))
                            ) : (
                                <p className="text-gray-500 dark:text-gray-400 text-center py-4">
                                    No pending leave requests
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        ⚡ Quick Actions
                    </h2>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link href={route('admin.users.index')}>
                            <Button className="w-full" variant="outline">
                                👥 Manage Users
                            </Button>
                        </Link>
                        <Link href={route('admin.classes.index')}>
                            <Button className="w-full" variant="outline">
                                🏫 Manage Classes
                            </Button>
                        </Link>
                        <Link href={route('admin.subjects.index')}>
                            <Button className="w-full" variant="outline">
                                📚 Manage Subjects
                            </Button>
                        </Link>
                        <Link href={route('attendance.index')}>
                            <Button className="w-full" variant="outline">
                                📊 View Reports
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </AppShell>
    );
}