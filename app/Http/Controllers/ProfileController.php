<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BadgeService;
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
        $user->load('badges');

        $uploadCount = $user->uploadCount();
        $earnedBadges = $user->earnedBadges();

        return view('pages.public-profile', compact(
            'user',
            'uploadCount',
            'earnedBadges',
        ));
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load('badges');

        $uploadCount = $user->uploadCount();
        $downloadCount = $user->downloadCount();
        $savedCount = $user->savedCount();

        $earnedBadges = $user->earnedBadges();
        $contributionProgress = $this->badgeService->getContributionProgress($user);

        return view('pages.profile', compact(
            'user',
            'uploadCount',
            'downloadCount',
            'savedCount',
            'earnedBadges',
            'contributionProgress',
        ));
    }
}
