<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentFile>
 */
class DocumentFileFactory extends Factory
{
    public function definition(): array
    {
        $extension = fake()->randomElement(DocumentFile::ALLOWED_EXTENSIONS);

        return [
            'document_id' => Document::factory(),
            'original_name' => fake()->word().'.'.$extension,
            'storage_path' => 'documents/1/'.fake()->uuid().'.'.$extension,
            'size_bytes' => fake()->numberBetween(1024, 5 * 1024 * 1024),
            'mime_type' => fake()->randomElement(DocumentFile::ALLOWED_MIMES),
            'extension' => $extension,
        ];
    }
}
