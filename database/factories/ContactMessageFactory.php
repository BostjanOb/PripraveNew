<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(5),
            'message' => fake()->paragraph(),
        ];
    }

    public function replied(): static
    {
        return $this->state(fn (): array => [
            'reply_message' => fake()->paragraph(),
            'replied_at' => fake()->dateTimeBetween('-7 days'),
            'replied_by' => User::factory(),
        ]);
    }
}
