<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $palette = array_keys(Faq::iconColorPalette());

        return [
            'question' => fake()->sentence(6),
            'answer' => fake()->paragraph(),
            'icon' => fake()->randomElement([
                'magnifying-glass',
                'upload',
                'download',
                'user',
                'star',
                'comment-alt-captions',
                'shield-check',
                'book-open',
            ]),
            'icon_background_color' => fake()->randomElement($palette),
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }
}
