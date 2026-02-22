<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\BadgeRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserBadge>
 */
class UserBadgeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'badge_id' => fake()->randomElement(BadgeRegistry::ids()),
            'earned_at' => now(),
        ];
    }
}
