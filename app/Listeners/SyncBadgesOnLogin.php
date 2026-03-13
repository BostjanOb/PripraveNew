<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Auth\Events\Login;

class SyncBadgesOnLogin
{
    public function __construct(private readonly BadgeService $badgeService) {}

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->badgeService->syncBadges($event->user);
    }
}
