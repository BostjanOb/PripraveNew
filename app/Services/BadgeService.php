<?php

namespace App\Services;

use App\Enums\Badge;
use App\Models\User;
use App\Models\UserBadge;

class BadgeService
{
    /**
     * Award a single badge idempotently.
     */
    public function awardBadge(User $user, Badge $badge): void
    {
        UserBadge::firstOrCreate(
            ['user_id' => $user->id, 'badge_id' => $badge],
            ['earned_at' => now()],
        );
    }

    /**
     * Compute all earned badges and persist any missing ones (never revoke).
     */
    public function syncBadges(User $user): void
    {
        $earned = $this->computeEarnedBadges($user);

        $existing = $user->badges->pluck('badge_id')->all();

        $missing = array_diff(
            array_map(fn (Badge $b) => $b->value, $earned),
            array_map(fn ($b) => $b instanceof Badge ? $b->value : $b, $existing),
        );

        foreach ($missing as $badgeValue) {
            $this->awardBadge($user, Badge::from($badgeValue));
        }

        // Refresh the relationship so subsequent reads are up-to-date
        $user->unsetRelation('badges');
    }

    /**
     * Compute which badges a user has earned based on their activity.
     *
     * @return Badge[]
     */
    public function computeEarnedBadges(User $user): array
    {
        $earned = [];

        // Contribution
        $uploadCount = $user->uploadCount();
        foreach (Badge::contributionMilestones() as $milestone) {
            if ($uploadCount >= $milestone['threshold']) {
                $earned[] = $milestone['badge'];
            }
        }

        // Downloads
        $downloadCount = $user->downloadCount();
        if ($downloadCount >= 10) {
            $earned[] = Badge::Raziskovalec;
        }
        if ($downloadCount >= 50) {
            $earned[] = Badge::Zbiratelj;
        }
        if ($downloadCount >= 200) {
            $earned[] = Badge::ModraSovica;
        }

        // Special
        if ($user->distinctSubjectCount() >= 3) {
            $earned[] = Badge::Vsestranski;
        }
        if ($user->commentCount() >= 10) {
            $earned[] = Badge::Pomocnik;
        }
        if ($user->maxDocumentDownloads() >= 100) {
            $earned[] = Badge::Navdih;
        }
        if ($user->isPioneer()) {
            $earned[] = Badge::Pionir;
        }

        return $earned;
    }

    /**
     * Check and award contribution badges after a document is created.
     */
    public function checkContributionBadges(User $user): void
    {
        $uploadCount = $user->uploadCount();

        foreach (Badge::contributionMilestones() as $milestone) {
            if ($uploadCount >= $milestone['threshold']) {
                $this->awardBadge($user, $milestone['badge']);
            }
        }

        // Also check vsestranski
        if ($user->distinctSubjectCount() >= 3) {
            $this->awardBadge($user, Badge::Vsestranski);
        }

        // Check pioneer
        if ($user->isPioneer()) {
            $this->awardBadge($user, Badge::Pionir);
        }
    }

    /**
     * Check and award download badges after a download is recorded.
     */
    public function checkDownloadBadges(User $user): void
    {
        $downloadCount = $user->downloadCount();

        if ($downloadCount >= 10) {
            $this->awardBadge($user, Badge::Raziskovalec);
        }
        if ($downloadCount >= 50) {
            $this->awardBadge($user, Badge::Zbiratelj);
        }
        if ($downloadCount >= 200) {
            $this->awardBadge($user, Badge::ModraSovica);
        }
    }

    /**
     * Check and award the navdih badge to the document author.
     */
    public function checkNavdihBadge(User $author): void
    {
        if ($author->maxDocumentDownloads() >= 100) {
            $this->awardBadge($author, Badge::Navdih);
        }
    }

    /**
     * Check and award the pomocnik badge.
     */
    public function checkCommentBadges(User $user): void
    {
        if ($user->commentCount() >= 10) {
            $this->awardBadge($user, Badge::Pomocnik);
        }
    }

    /**
     * Return contribution progress data for the "Moj vpliv" card.
     *
     * @return array{uploadCount: int, nextBadge: Badge|null, uploadsToNext: int, previousMilestone: int, nextMilestone: int|null, progressPercent: float}
     */
    public function getContributionProgress(User $user): array
    {
        $uploadCount = $user->uploadCount();
        $milestones = Badge::contributionMilestones();

        $nextMilestone = null;
        $nextIndex = -1;

        foreach ($milestones as $index => $milestone) {
            if ($uploadCount < $milestone['threshold']) {
                $nextMilestone = $milestone;
                $nextIndex = $index;
                break;
            }
        }

        $previousMilestone = $nextIndex > 0 ? $milestones[$nextIndex - 1]['threshold'] : 0;
        $currentThreshold = $nextMilestone
            ? $nextMilestone['threshold']
            : $milestones[count($milestones) - 1]['threshold'];

        $uploadsToNext = $nextMilestone ? $currentThreshold - $uploadCount : 0;

        $progressPercent = $nextMilestone
            ? max(0, min(100, (($uploadCount - $previousMilestone) / ($currentThreshold - $previousMilestone)) * 100))
            : 100.0;

        return [
            'uploadCount' => $uploadCount,
            'nextBadge' => $nextMilestone ? $nextMilestone['badge'] : null,
            'uploadsToNext' => $uploadsToNext,
            'previousMilestone' => $previousMilestone,
            'nextMilestone' => $nextMilestone ? $currentThreshold : null,
            'progressPercent' => $progressPercent,
        ];
    }
}
