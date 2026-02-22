<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Document;
use App\Models\ReportReason;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'user_id' => User::factory(),
            'report_reason_id' => ReportReason::factory(),
            'message' => fake()->optional()->paragraph(),
            'status' => ReportStatus::Pending,
        ];
    }
}
