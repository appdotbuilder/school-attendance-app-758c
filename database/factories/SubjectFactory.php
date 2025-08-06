<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'Physics', 'code' => 'PHYS'],
            ['name' => 'Chemistry', 'code' => 'CHEM'],
            ['name' => 'Biology', 'code' => 'BIOL'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'History', 'code' => 'HIST'],
            ['name' => 'Geography', 'code' => 'GEO'],
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Art', 'code' => 'ART'],
            ['name' => 'Physical Education', 'code' => 'PE'],
        ];
        
        $subject = fake()->randomElement($subjects);
        $grade = fake()->numberBetween(9, 12);
        
        return [
            'name' => $subject['name'],
            'code' => $subject['code'] . $grade,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}