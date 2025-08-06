<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fromDate = fake()->dateTimeBetween('now', '+1 month');
        $toDate = fake()->dateTimeBetween($fromDate, '+1 month');
        
        return [
            'student_id' => User::factory()->student(),
            'from_date' => $fromDate->format('Y-m-d'),
            'to_date' => $toDate->format('Y-m-d'),
            'type' => fake()->randomElement(['sick', 'family', 'personal', 'emergency']),
            'reason' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'admin_notes' => fake()->optional()->sentence(),
            'reviewed_by' => fake()->boolean() ? User::factory()->admin() : null,
            'reviewed_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}