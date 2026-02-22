<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a user with required display name and accepted terms', function () {
    $this->post(route('register'), [
        'display_name' => 'Janez Novak',
        'email' => 'janez@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => 'on',
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
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['display_name', 'terms']);

    expect(User::query()->count())->toBe(0);
});
