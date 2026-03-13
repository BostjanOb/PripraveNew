<?php

namespace Database\Factories;

use App\Enums\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBadge>
 */
class UserBadgeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'badge_id' => fake()->randomElement(Badge::cases()),
            'earned_at' => now(),
        ];
    }
}
