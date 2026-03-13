<?php

use App\Enums\Badge;
use App\Enums\BadgeCategory;
use App\Models\Document;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('awards novinec badge on user creation', function () {
    $user = User::factory()->create();

    expect($user->hasBadge(Badge::Novinec))->toBeTrue();
});

it('syncs contribution badges based on upload count', function () {
    $user = User::factory()->create();

    Document::factory()->count(5)->for($user)->create();

    app(BadgeService::class)->syncBadges($user);

    expect($user->hasBadge(Badge::PrviKorak))->toBeTrue()
        ->and($user->hasBadge(Badge::Prispevkar))->toBeTrue()
        ->and($user->hasBadge(Badge::AktivniAvtor))->toBeFalse();
});

it('never revokes badges during sync', function () {
    $user = User::factory()->create();

    // Manually award a badge
    app(BadgeService::class)->awardBadge($user, Badge::MojsterPriprav);

    // Sync should not remove it even though user has 0 uploads
    app(BadgeService::class)->syncBadges($user);
    $user->unsetRelation('badges');

    expect($user->hasBadge(Badge::MojsterPriprav))->toBeTrue();
});

it('award badge is idempotent', function () {
    $user = User::factory()->create();
    $service = app(BadgeService::class);

    $service->awardBadge($user, Badge::Raziskovalec);
    $service->awardBadge($user, Badge::Raziskovalec);

    expect($user->badges()->where('badge_id', Badge::Raziskovalec)->count())->toBe(1);
});

it('returns correct contribution progress', function () {
    $user = User::factory()->create();

    $progress = app(BadgeService::class)->getContributionProgress($user);

    expect($progress['uploadCount'])->toBe(0)
        ->and($progress['nextBadge'])->toBe(Badge::PrviKorak)
        ->and($progress['uploadsToNext'])->toBe(1)
        ->and($progress['progressPercent'])->toEqual(0.0);
});

it('has all 16 badges across 4 categories', function () {
    expect(Badge::cases())->toHaveCount(16)
        ->and(BadgeCategory::cases())->toHaveCount(4);

    foreach (BadgeCategory::cases() as $category) {
        expect(Badge::forCategory($category))->not->toBeEmpty();
    }
});

it('returns highest contribution badge correctly', function () {
    expect(Badge::highestContributionBadge(0))->toBeNull()
        ->and(Badge::highestContributionBadge(1))->toBe(Badge::PrviKorak)
        ->and(Badge::highestContributionBadge(5))->toBe(Badge::Prispevkar)
        ->and(Badge::highestContributionBadge(15))->toBe(Badge::AktivniAvtor)
        ->and(Badge::highestContributionBadge(50))->toBe(Badge::ZvezdaSkupnosti)
        ->and(Badge::highestContributionBadge(100))->toBe(Badge::MojsterPriprav)
        ->and(Badge::highestContributionBadge(999))->toBe(Badge::MojsterPriprav);
});
