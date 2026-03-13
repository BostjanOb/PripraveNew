<?php

namespace App\Observers;

use App\Models\DownloadRecord;
use App\Services\BadgeService;

class DownloadRecordObserver
{
    public function __construct(private readonly BadgeService $badgeService) {}

    public function created(DownloadRecord $downloadRecord): void
    {
        // Award download badges to the downloader
        if ($downloadRecord->user) {
            $this->badgeService->checkDownloadBadges($downloadRecord->user);
        }

        // Award navdih badge to the document author
        $author = $downloadRecord->document?->user;
        if ($author) {
            $this->badgeService->checkNavdihBadge($author);
        }
    }
}
