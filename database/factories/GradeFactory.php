<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\SchoolType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_type_id' => SchoolType::factory(),
            'name' => fake()->numberBetween(1, 9).'. razred',
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
