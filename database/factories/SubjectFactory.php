<?php

namespace Database\Factories;

use App\Models\SchoolType;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function forSchoolType(SchoolType|int $schoolType): static
    {
        return $this->afterCreating(function (Subject $subject) use ($schoolType): void {
            $schoolTypeId = $schoolType instanceof SchoolType ? $schoolType->id : $schoolType;
            $subject->schoolTypes()->syncWithoutDetaching([(int) $schoolTypeId]);
        });
    }
}
