<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grade = fake()->numberBetween(9, 12);
        $section = fake()->randomElement(['A', 'B', 'C', 'D']);
        
        return [
            'name' => $grade . '-' . $section,
            'grade' => (string) $grade,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}