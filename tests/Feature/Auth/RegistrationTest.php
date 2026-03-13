<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RyanChandler\LaravelCloudflareTurnstile\Facades\Turnstile;

uses(RefreshDatabase::class);

beforeEach(function () {
    Turnstile::fake();
});

it('shows the turnstile widget on the registration page', function () {
    $this->get(route('register'))
        ->assertSuccessful()
        ->assertSee('challenges.cloudflare.com/turnstile/v0/api.js', false)
        ->assertSee('cf-turnstile', false);
});

it('creates a user with required display name and accepted terms', function () {
    $this->post(route('register'), [
        'display_name' => 'Janez Novak',
        'email' => 'janez@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => 'on',
        'cf-turnstile-response' => Turnstile::dummy(),
    ])->assertRedirect();

    $this->assertDatabaseHas('users', [
        'display_name' => 'Janez Novak',
        'name' => 'Janez Novak',
        'email' => 'janez@example.com',
    ]);

    expect(auth()->check())->toBeTrue();
});

it('requires display name and terms for registration', function () {
    $this->from(route('register'))
        ->post(route('register'), [
            'display_name' => '',
            'email' => 'janez@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'cf-turnstile-response' => Turnstile::dummy(),
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['display_name', 'terms']);

    expect(User::query()->count())->toBe(0);
});

it('requires a valid turnstile response for registration', function () {
    Turnstile::fake()->fail();

    $this->from(route('register'))
        ->post(route('register'), [
            'display_name' => 'Janez Novak',
            'email' => 'janez@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => 'on',
            'cf-turnstile-response' => Turnstile::dummy(),
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['cf-turnstile-response']);

    expect(User::query()->count())->toBe(0);
});
