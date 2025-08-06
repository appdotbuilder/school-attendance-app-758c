<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'student_id' => null,
        ]);

        // Create teachers
        $teachers = User::factory()->count(5)->teacher()->create();

        // Create subjects
        $subjects = collect([
            ['name' => 'Mathematics', 'code' => 'MATH9'],
            ['name' => 'Physics', 'code' => 'PHYS10'],
            ['name' => 'Chemistry', 'code' => 'CHEM10'],
            ['name' => 'Biology', 'code' => 'BIOL11'],
            ['name' => 'English', 'code' => 'ENG9'],
            ['name' => 'History', 'code' => 'HIST10'],
            ['name' => 'Geography', 'code' => 'GEO11'],
            ['name' => 'Computer Science', 'code' => 'CS12'],
        ])->map(function ($subjectData) {
            return Subject::create([
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'description' => 'A comprehensive course in ' . $subjectData['name'],
                'is_active' => true,
            ]);
        });

        // Create classes
        $classes = collect([
            ['name' => '9-A', 'grade' => '9'],
            ['name' => '9-B', 'grade' => '9'],
            ['name' => '10-A', 'grade' => '10'],
            ['name' => '10-B', 'grade' => '10'],
            ['name' => '11-A', 'grade' => '11'],
            ['name' => '12-A', 'grade' => '12'],
        ])->map(function ($classData) {
            return SchoolClass::create([
                'name' => $classData['name'],
                'grade' => $classData['grade'],
                'description' => 'Grade ' . $classData['grade'] . ' class',
                'is_active' => true,
            ]);
        });

        // Create students
        $students = User::factory()->count(50)->student()->create();

        // Assign students to classes
        $classes->each(function ($class) use ($students) {
            $classStudents = $students->random(random_int(8, 12));
            $class->students()->attach($classStudents->pluck('id'), [
                'enrolled_at' => now(),
                'is_active' => true,
            ]);
        });

        // Create schedules
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timeSlots = [
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:30', 'end' => '11:30'],
            ['start' => '11:30', 'end' => '12:30'],
            ['start' => '13:30', 'end' => '14:30'],
            ['start' => '14:30', 'end' => '15:30'],
        ];

        $classes->each(function ($class) use ($subjects, $teachers, $days, $timeSlots) {
            // Create schedule for each class
            collect($days)->each(function ($day) use ($class, $subjects, $teachers, $timeSlots) {
                $dailySlots = collect($timeSlots)->random(random_int(3, 5));
                
                $dailySlots->each(function ($slot) use ($class, $subjects, $teachers, $day) {
                    Schedule::create([
                        'class_id' => $class->id,
                        'subject_id' => $subjects->random()->id,
                        'teacher_id' => $teachers->random()->id,
                        'day_of_week' => $day,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'room' => 'Room ' . random_int(101, 305),
                        'is_active' => true,
                    ]);
                });
            });
        });

        // Create attendance records for the past month
        $schedules = Schedule::with(['schoolClass.students'])->get();
        
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayOfWeek = strtolower($date->format('l'));
            
            // Skip weekends
            if (in_array($dayOfWeek, ['saturday', 'sunday'])) {
                continue;
            }
            
            $todaySchedules = $schedules->where('day_of_week', $dayOfWeek);
            
            foreach ($todaySchedules as $schedule) {
                foreach ($schedule->schoolClass->students as $student) {
                    // 85% chance of being present
                    $status = random_int(1, 100) <= 85 ? 'present' : 'absent';
                    
                    // 10% chance of being late if present
                    if ($status === 'present' && random_int(1, 100) <= 10) {
                        $status = 'late';
                    }
                    
                    Attendance::create([
                        'student_id' => $student->id,
                        'schedule_id' => $schedule->id,
                        'date' => $date->format('Y-m-d'),
                        'status' => $status,
                        'notes' => $status === 'late' ? 'Arrived 10 minutes late' : null,
                        'recorded_by' => $schedule->teacher_id,
                    ]);
                }
            }
        }

        // Create some leave requests
        LeaveRequest::factory()->count(15)->create([
            'student_id' => $students->random()->id,
        ]);
    }
}