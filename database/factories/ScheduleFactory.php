<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 15);
        $startTime = sprintf('%02d:00', $startHour);
        $endTime = sprintf('%02d:00', $startHour + 1);
        
        return [
            'class_id' => SchoolClass::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory()->teacher(),
            'day_of_week' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room' => fake()->bothify('Room ###'),
            'is_active' => true,
        ];
    }
}