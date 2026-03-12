<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DownloadRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DownloadRecord>
 */
class DownloadRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'document_id' => Document::factory(),
            'document_file_id' => null,
        ];
    }
}
