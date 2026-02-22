<?php

namespace App\Services;

use App\Models\User;
use App\Support\BadgeRegistry;

class BadgeService
{
    /**
     * Compute which badge IDs a user has earned based on their activity.
     *
     * @return string[]
     */
    public function getEarnedBadgeIds(User $user): array
    {
        $uploadCount = $user->uploadCount();
        $downloadCount = $user->downloadCount();
        $memberYears = $user->memberYears();
        $subjectsCount = $user->distinctSubjectCount();
        $commentCount = $user->commentCount();
        $maxDocDownloads = $user->maxDocumentDownloads();
        $isPioneer = $user->isPioneer();

        $earned = [];

        // Contribution
        if ($uploadCount >= 1) {
            $earned[] = 'prvi-korak';
        }
        if ($uploadCount >= 5) {
            $earned[] = 'prispevkar';
        }
        if ($uploadCount >= 15) {
            $earned[] = 'aktivni-avtor';
        }
        if ($uploadCount >= 50) {
            $earned[] = 'zvezda-skupnosti';
        }
        if ($uploadCount >= 100) {
            $earned[] = 'mojster-priprav';
        }

        // Downloads
        if ($downloadCount >= 10) {
            $earned[] = 'raziskovalec';
        }
        if ($downloadCount >= 50) {
            $earned[] = 'zbiratelj';
        }
        if ($downloadCount >= 200) {
            $earned[] = 'modra-sovica';
        }

        // Loyalty — everyone gets novinec
        $earned[] = 'novinec';
        if ($memberYears >= 1) {
            $earned[] = '1-leto';
        }
        if ($memberYears >= 2) {
            $earned[] = '2-leti';
        }
        if ($memberYears >= 5) {
            $earned[] = 'veteran';
        }

        // Special
        if ($subjectsCount >= 3) {
            $earned[] = 'vsestranski';
        }
        if ($commentCount >= 10) {
            $earned[] = 'pomocnik';
        }
        if ($maxDocDownloads >= 100) {
            $earned[] = 'navdih';
        }
        if ($isPioneer) {
            $earned[] = 'pionir';
        }

        return $earned;
    }

    /**
     * Return contribution progress data for the "Moj vpliv" card.
     *
     * @return array{uploadCount: int, nextBadge: array{id: string, name: string, description: string, category: string, requirement: string, color: array{bg: string, border: string, text: string, iconBg: string}}|null, uploadsToNext: int, previousMilestone: int, nextMilestone: int|null, progressPercent: float}
     */
    public function getContributionProgress(User $user): array
    {
        $uploadCount = $user->uploadCount();

        $milestones = [
            ['id' => 'prvi-korak', 'uploads' => 1],
            ['id' => 'prispevkar', 'uploads' => 5],
            ['id' => 'aktivni-avtor', 'uploads' => 15],
            ['id' => 'zvezda-skupnosti', 'uploads' => 50],
            ['id' => 'mojster-priprav', 'uploads' => 100],
        ];

        $nextMilestone = null;
        $nextIndex = -1;

        foreach ($milestones as $index => $milestone) {
            if ($uploadCount < $milestone['uploads']) {
                $nextMilestone = $milestone;
                $nextIndex = $index;
                break;
            }
        }

        $previousMilestone = $nextIndex > 0 ? $milestones[$nextIndex - 1]['uploads'] : 0;
        $currentMilestone = $nextMilestone
            ? $nextMilestone['uploads']
            : $milestones[count($milestones) - 1]['uploads'];

        $uploadsToNext = $nextMilestone ? $currentMilestone - $uploadCount : 0;

        $progressPercent = $nextMilestone
            ? max(0, min(100, (($uploadCount - $previousMilestone) / ($currentMilestone - $previousMilestone)) * 100))
            : 100.0;

        $nextBadgeDefinition = $nextMilestone ? BadgeRegistry::find($nextMilestone['id']) : null;

        return [
            'uploadCount' => $uploadCount,
            'nextBadge' => $nextBadgeDefinition,
            'uploadsToNext' => $uploadsToNext,
            'previousMilestone' => $previousMilestone,
            'nextMilestone' => $nextMilestone ? $currentMilestone : null,
            'progressPercent' => $progressPercent,
        ];
    }
}
