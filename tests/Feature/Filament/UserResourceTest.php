<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

it('loads the users resource page', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(ListUsers::class)
        ->assertOk()
        ->assertCanSeeTableRecords($users);
});

it('creates a user via header action', function () {
    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'display_name' => 'Nova Uporabnica',
            'name' => 'Nova Uporabnica',
            'email' => 'nova@example.com',
            'role' => 'user',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

    expect(User::query()->where('email', 'nova@example.com')->exists())->toBeTrue();
});

it('updates a user via table action', function () {
    $user = User::factory()->create([
        'display_name' => 'Staro Ime',
        'name' => 'Staro Ime',
        'role' => 'user',
    ]);

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make(EditAction::class)->table($user), data: [
            'display_name' => 'Novo Ime',
            'name' => 'Novo Ime',
            'email' => $user->email,
            'role' => 'admin',
            'password' => '',
            'password_confirmation' => '',
        ]);

    expect($user->fresh()->display_name)->toBe('Novo Ime');
    expect($user->fresh()->role)->toBe('admin');
});

it('shows user infolist on the view page', function () {
    $user = User::factory()->create([
        'display_name' => 'Testni Uporabnik',
        'email' => 'testni@example.com',
    ]);

    Livewire::test(ViewUser::class, ['record' => $user->getRouteKey()])
        ->assertOk()
        ->assertSee('Testni Uporabnik')
        ->assertSee('testni@example.com');
});

it('hides delete action for the currently authenticated user', function () {
    /** @var User $admin */
    $admin = auth()->user();

    Livewire::test(ListUsers::class)
        ->assertActionHidden(TestAction::make(DeleteAction::class)->table($admin));
});

it('does not delete the currently authenticated user in bulk delete', function () {
    /** @var User $admin */
    $admin = auth()->user();
    $otherUser = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->callTableBulkAction('delete', [$admin, $otherUser]);

    expect($admin->fresh())->not->toBeNull();
    expect($otherUser->fresh())->toBeNull();
});

it('allows admin to access admin dashboard', function () {
    $this->get('/admin')->assertSuccessful();
});

it('denies non-admin users from accessing admin dashboard', function () {
    $this->actingAs(User::factory()->create(['role' => 'user']))
        ->get('/admin')
        ->assertForbidden();
});
