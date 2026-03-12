<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('shows remember me option on login page', function (): void {
    get(route('login'))
        ->assertOk()
        ->assertSee('name="remember"', false)
        ->assertSee('Zapomni si me');
});

it('updates the last login timestamp after a successful password login', function (): void {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'last_login_at' => null,
    ]);

    $now = Carbon::parse('2026-03-12 10:15:00');

    Carbon::setTestNow($now);

    try {
        post('/prijava', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/profil');
    } finally {
        Carbon::setTestNow();
    }

    $this->assertAuthenticatedAs($user);

    expect($user->fresh()->last_login_at?->toDateTimeString())->toBe('2026-03-12 10:15:00');
});

it('does not update the last login timestamp after a failed password login', function (): void {
    $user = User::factory()->create([
        'email' => 'failed@example.com',
        'last_login_at' => Carbon::parse('2026-03-10 08:00:00'),
    ]);

    $now = Carbon::parse('2026-03-12 10:30:00');

    Carbon::setTestNow($now);

    try {
        post('/prijava', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    } finally {
        Carbon::setTestNow();
    }

    $this->assertGuest();

    expect($user->fresh()->last_login_at?->toDateTimeString())->toBe('2026-03-10 08:00:00');
});
