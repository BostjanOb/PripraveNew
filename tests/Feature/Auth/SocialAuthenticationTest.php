<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

it('redirects to google provider', function () {
    Socialite::fake('google');

    $this->get('/auth/google/redirect')->assertRedirect();
});

it('creates a user when logging in with google for the first time', function () {
    $now = Carbon::parse('2026-03-12 11:00:00');

    Carbon::setTestNow($now);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-123',
        'name' => 'Google User',
        'email' => 'google-user@example.com',
    ]));

    try {
        $this->get('/auth/google/callback')->assertRedirect('/profil');
    } finally {
        Carbon::setTestNow();
    }

    $this->assertDatabaseHas('users', [
        'email' => 'google-user@example.com',
        'google_id' => 'google-123',
        'last_login_at' => '2026-03-12 11:00:00',
    ]);

    expect(auth()->check())->toBeTrue();
});

it('links google account to existing user by email', function () {
    $user = User::factory()->create([
        'display_name' => 'Existing User',
        'slug' => 'existing-user',
        'email' => 'existing@example.com',
        'google_id' => null,
        'last_login_at' => null,
    ]);

    $now = Carbon::parse('2026-03-12 11:15:00');

    Carbon::setTestNow($now);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-555',
        'name' => 'Existing User',
        'email' => 'existing@example.com',
    ]));

    try {
        $this->get('/auth/google/callback')->assertRedirect('/profil');
    } finally {
        Carbon::setTestNow();
    }

    expect($user->fresh()->google_id)->toBe('google-555')
        ->and($user->fresh()->last_login_at?->toDateTimeString())->toBe('2026-03-12 11:15:00');
});

it('logs in existing user by google id', function () {
    $user = User::factory()->create([
        'display_name' => 'Google Id User',
        'slug' => 'google-id-user',
        'google_id' => 'google-777',
    ]);

    Socialite::fake('google', (new SocialiteUser)->map([
        'id' => 'google-777',
        'name' => 'Google Id User',
        'email' => 'another@example.com',
    ]));

    $this->get('/auth/google/callback')->assertRedirect('/profil');

    expect(auth()->id())->toBe($user->id);
});

it('creates a placeholder email for facebook when email is missing', function () {
    Socialite::fake('facebook', (new SocialiteUser)->map([
        'id' => 'fb-123',
        'name' => 'Facebook User',
        'email' => null,
    ]));

    $this->get('/auth/facebook/callback')->assertRedirect('/profil');

    $this->assertDatabaseHas('users', [
        'facebook_id' => 'fb-123',
        'email' => 'facebook-fb-123@placeholder.priprave.net',
    ]);
});

it('returns to login on social callback failure', function () {
    Socialite::fake('google', fn () => throw new RuntimeException('OAuth failed'));

    $this->get('/auth/google/callback')
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');
});
