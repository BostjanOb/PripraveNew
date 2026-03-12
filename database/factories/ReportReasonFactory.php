<?php

namespace Database\Factories;

use App\Models\ReportReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportReason>
 */
class ReportReasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
