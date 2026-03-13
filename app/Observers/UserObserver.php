<?php

namespace App\Observers;

use App\Enums\Badge;
use App\Models\User;
use App\Services\BadgeService;

class UserObserver
{
    public function __construct(private readonly BadgeService $badgeService) {}

    public function created(User $user): void
    {
        $this->badgeService->awardBadge($user, Badge::Novinec);
    }
}
