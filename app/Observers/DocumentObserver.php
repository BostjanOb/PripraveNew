<?php

namespace App\Observers;

use App\Models\Document;
use App\Services\BadgeService;

class DocumentObserver
{
    public function __construct(private readonly BadgeService $badgeService) {}

    public function created(Document $document): void
    {
        if ($document->user) {
            $this->badgeService->checkContributionBadges($document->user);
        }
    }
}
