<?php

use App\Models\User;
use App\Models\UserBadge;

it('computes initials from display name', function () {
    $fullNameUser = (new User)->forceFill(['display_name' => 'Ana Novak']);
    $singleNameUser = (new User)->forceFill(['display_name' => 'Žan']);

    expect($fullNameUser->initials)->toBe('AN')
        ->and($singleNameUser->initials)->toBe('ŽA');
});

it('returns null avatar url when avatar path does not exist', function () {
    $user = (new User)->forceFill(['avatar_path' => null]);

    expect($user->avatar_url)->toBeNull();
});

it('resolves badge definition from badge id', function () {
    $userBadge = (new UserBadge)->forceFill(['badge_id' => 'prvi-korak']);

    expect($userBadge->definition)->toBeArray()
        ->and($userBadge->definition['id'])->toBe('prvi-korak')
        ->and($userBadge->definition['name'])->toBeString();
});

it('returns null badge definition for unknown badge id', function () {
    $userBadge = (new UserBadge)->forceFill(['badge_id' => 'missing-badge']);

    expect($userBadge->definition)->toBeNull();
});
