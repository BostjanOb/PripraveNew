<?php

use App\Models\User;

it('builds avatar url when avatar path exists', function () {
    $user = (new User)->forceFill(['avatar_path' => 'avatars/user.jpg']);

    expect($user->avatar_url)->toBe(asset('storage/avatars/user.jpg'));
});
