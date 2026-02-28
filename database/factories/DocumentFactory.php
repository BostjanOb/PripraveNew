<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->optional()->paragraph(),
            'topic' => fake()->optional()->sentence(4),
            'keywords' => fake()->optional()->words(5, true),
            'category_id' => Category::factory(),
            'school_type_id' => SchoolType::factory(),
            'grade_id' => Grade::factory(),
            'subject_id' => Subject::factory(),
            'user_id' => User::factory(),
            'views_count' => fake()->numberBetween(0, 500),
            'downloads_count' => fake()->numberBetween(0, 200),
            'rating_count' => 0,
            'rating_avg' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Document $document): void {
            if ($document->grade?->school_type_id !== $document->school_type_id) {
                $document->grade_id = Grade::factory()->create([
                    'school_type_id' => $document->school_type_id,
                ])->id;
            }

            if (
                $document->subject !== null
                && ! $document->subject->schoolTypes()->whereKey($document->school_type_id)->exists()
            ) {
                $document->subject->schoolTypes()->syncWithoutDetaching([$document->school_type_id]);
            }

            if ($document->isDirty('grade_id')) {
                $document->save();
            }
        });
    }
}
