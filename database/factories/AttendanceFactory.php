<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'schedule_id' => Schedule::factory(),
            'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status' => fake()->randomElement(['present', 'absent', 'late', 'excused']),
            'notes' => fake()->optional()->sentence(),
            'recorded_by' => User::factory()->teacher(),
        ];
    }
}