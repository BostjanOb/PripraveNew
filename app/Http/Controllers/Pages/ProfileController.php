<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BadgeService;
use App\Support\BadgeRegistry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly BadgeService $badgeService) {}

    public function edit(): View
    {
        return view('pages.edit-profile');
    }

    public function show(User $user): View
    {
        $uploadCount = $user->uploadCount();
        $earnedBadgeIds = $this->badgeService->getEarnedBadgeIds($user);

        $allBadges = BadgeRegistry::all();
        $categories = BadgeRegistry::categories();
        $categoryLabels = BadgeRegistry::categoryLabels();

        return view('pages.public-profile', compact(
            'user',
            'uploadCount',
            'earnedBadgeIds',
            'allBadges',
            'categories',
            'categoryLabels',
        ));
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $uploadCount = $user->uploadCount();
        $downloadCount = $user->downloadCount();
        $savedCount = $user->savedCount();

        $earnedBadgeIds = $this->badgeService->getEarnedBadgeIds($user);
        $contributionProgress = $this->badgeService->getContributionProgress($user);

        $allBadges = BadgeRegistry::all();
        $categories = BadgeRegistry::categories();
        $categoryLabels = BadgeRegistry::categoryLabels();

        return view('pages.profile', compact(
            'user',
            'uploadCount',
            'downloadCount',
            'savedCount',
            'earnedBadgeIds',
            'contributionProgress',
            'allBadges',
            'categories',
            'categoryLabels',
        ));
    }
}
