import React, { useState } from 'react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Head, router, useForm } from '@inertiajs/react';

interface Schedule {
    id: number;
    start_time: string;
    end_time: string;
    room: string;
    school_class: {
        id: number;
        name: string;
    };
    subject: {
        name: string;
        code: string;
    };
}

interface Student {
    id: number;
    name: string;
    student_id: string;
    status: string;
    notes: string;
}

interface Props {
    schedule: Schedule;
    students: Student[];
    date: string;
    [key: string]: unknown;
}

export default function CreateAttendance({ schedule, students, date }: Props) {
    const [attendanceData, setAttendanceData] = useState(
        students.reduce((acc, student) => ({
            ...acc,
            [student.id]: {
                status: student.status,
                notes: student.notes,
            },
        }), {} as Record<number, { status: string; notes: string }>)
    );

    const { processing } = useForm({
        attendance: [] as Array<{
            student_id: number;
            status: string;
            notes: string;
        }>,
        schedule_id: schedule.id,
        date: date,
    });

    const handleStatusChange = (studentId: number, status: string) => {
        setAttendanceData(prev => ({
            ...prev,
            [studentId]: {
                ...prev[studentId],
                status,
            },
        }));
    };

    const handleNotesChange = (studentId: number, notes: string) => {
        setAttendanceData(prev => ({
            ...prev,
            [studentId]: {
                ...prev[studentId],
                notes,
            },
        }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        const attendance = students.map(student => ({
            student_id: student.id,
            status: attendanceData[student.id]?.status || 'absent',
            notes: attendanceData[student.id]?.notes || '',
        }));

        router.post(route('attendance.store'), {
            attendance,
            schedule_id: schedule.id,
            date,
        }, {
            preserveState: true,
            onSuccess: () => {
                router.visit(route('attendance.index'));
            },
        });
    };

    const formatTime = (timeString: string) => {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'present':
                return 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900 dark:text-green-300';
            case 'late':
                return 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900 dark:text-yellow-300';
            case 'excused':
                return 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900 dark:text-blue-300';
            default:
                return 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-300';
        }
    };

    return (
        <AppShell>
            <Head title="Take Attendance" />
            
            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-gray-100">
                        📝 Take Attendance
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400">
                        Record attendance for {schedule.subject.name} - Class {schedule.school_class.name}
                    </p>
                </div>

                {/* Schedule Info */}
                <div className="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Subject</p>
                            <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {schedule.subject.name}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Class</p>
                            <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {schedule.school_class.name}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Time</p>
                            <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {formatTime(schedule.start_time)} - {formatTime(schedule.end_time)}
                            </p>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                            <p className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {new Date(date).toLocaleDateString()}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Attendance Form */}
                <form onSubmit={handleSubmit}>
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Student Attendance ({students.length} students)
                            </h2>
                        </div>

                        <div className="p-6">
                            <div className="space-y-4">
                                {students.map((student) => (
                                    <div
                                        key={student.id}
                                        className="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                    >
                                        <div className="flex-1">
                                            <h3 className="font-medium text-gray-900 dark:text-gray-100">
                                                {student.name}
                                            </h3>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                                ID: {student.student_id}
                                            </p>
                                        </div>

                                        <div className="flex items-center space-x-4">
                                            {/* Status Selection */}
                                            <div className="flex space-x-2">
                                                {['present', 'absent', 'late', 'excused'].map((status) => (
                                                    <button
                                                        key={status}
                                                        type="button"
                                                        onClick={() => handleStatusChange(student.id, status)}
                                                        className={`px-3 py-2 rounded-lg text-sm font-medium border-2 transition-colors ${
                                                            (attendanceData[student.id]?.status || 'absent') === status
                                                                ? getStatusColor(status)
                                                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-500'
                                                        }`}
                                                    >
                                                        {status.charAt(0).toUpperCase() + status.slice(1)}
                                                    </button>
                                                ))}
                                            </div>

                                            {/* Notes Input */}
                                            <input
                                                type="text"
                                                placeholder="Notes (optional)"
                                                value={attendanceData[student.id]?.notes || ''}
                                                onChange={(e) => handleNotesChange(student.id, e.target.value)}
                                                className="w-32 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100"
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => router.visit(route('attendance.index'))}
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                disabled={processing}
                                className="bg-indigo-600 hover:bg-indigo-700"
                            >
                                {processing ? 'Saving...' : 'Save Attendance'}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </AppShell>
    );
}