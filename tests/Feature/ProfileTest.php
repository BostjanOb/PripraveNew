<?php

use App\Livewire\DownloadedDocumentsTab;
use App\Livewire\SavedDocumentsTab;
use App\Livewire\UploadedDocumentsTab;
use App\Models\Document;
use App\Models\DownloadRecord;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Profile page ──────────────────────────────────────────────────────────────

it('requires auth to view the profile page', function () {
    $this->get(route('profile'))->assertRedirect(route('login'));
});

it('shows the profile page to authenticated verified users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertSuccessful()
        ->assertSee($user->display_name);
});

it('redirects unverified users away from the profile page', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertRedirect(route('verification.notice'));
});

// ── Edit profile page ─────────────────────────────────────────────────────────

it('requires auth to view the edit profile page', function () {
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
});

it('shows the edit profile page to authenticated verified users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertSuccessful();
});

// ── BasicInfoForm ─────────────────────────────────────────────────────────────

it('can update display name and name', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('profile.basic-info-form')
        ->set('displayName', 'Novo Prikazno Ime')
        ->set('name', 'Novo Ime Priimek')
        ->call('saveBasicInfo')
        ->assertHasNoErrors();

    expect($user->fresh())
        ->display_name->toBe('Novo Prikazno Ime')
        ->name->toBe('Novo Ime Priimek');
});

it('does not change email when saving basic info', function () {
    $user = User::factory()->create(['email' => 'original@email.si']);

    Livewire::actingAs($user)
        ->test('profile.basic-info-form')
        ->set('displayName', 'Novo Ime')
        ->set('name', 'Ime Priimek')
        ->call('saveBasicInfo')
        ->assertHasNoErrors();

    expect($user->fresh()->email)->toBe('original@email.si');
});

it('validates display name is required when saving basic info', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('profile.basic-info-form')
        ->set('displayName', '')
        ->call('saveBasicInfo')
        ->assertHasErrors(['displayName' => 'required']);
});

it('validates display name minimum length when saving basic info', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('profile.basic-info-form')
        ->set('displayName', 'A')
        ->call('saveBasicInfo')
        ->assertHasErrors(['displayName' => 'min']);
});

it('validates name is required when saving basic info', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('profile.basic-info-form')
        ->set('name', '')
        ->call('saveBasicInfo')
        ->assertHasErrors(['name' => 'required']);
});

// ── ChangePasswordForm ────────────────────────────────────────────────────────

it('can change the password with correct current password', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);

    Livewire::actingAs($user)
        ->test('profile.change-password-form')
        ->set('currentPassword', 'old-password')
        ->set('newPassword', 'new-password-1!')
        ->set('confirmPassword', 'new-password-1!')
        ->call('changePassword')
        ->assertHasNoErrors();

    expect(Hash::check('new-password-1!', $user->fresh()->password))->toBeTrue();
});

it('rejects password change with wrong current password', function () {
    $user = User::factory()->create(['password' => Hash::make('correct')]);

    Livewire::actingAs($user)
        ->test('profile.change-password-form')
        ->set('currentPassword', 'wrong')
        ->set('newPassword', 'new-password-1!')
        ->set('confirmPassword', 'new-password-1!')
        ->call('changePassword')
        ->assertHasErrors(['currentPassword']);
});

it('rejects password change when confirmation does not match', function () {
    $user = User::factory()->create(['password' => Hash::make('correct')]);

    Livewire::actingAs($user)
        ->test('profile.change-password-form')
        ->set('currentPassword', 'correct')
        ->set('newPassword', 'new-password-1!')
        ->set('confirmPassword', 'different-password-1!')
        ->call('changePassword')
        ->assertHasErrors(['confirmPassword' => 'same']);
});

// ── AvatarUpload ──────────────────────────────────────────────────────────────

it('can upload an avatar image', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('profile.avatar-upload')
        ->set('avatar', UploadedFile::fake()->image('avatar.jpg', 200, 200))
        ->call('saveAvatar')
        ->assertHasNoErrors();

    Storage::disk('public')->assertExists($user->fresh()->avatar_path);
});

it('rejects avatar files that are too large', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('profile.avatar-upload')
        ->set('avatar', UploadedFile::fake()->image('big.jpg')->size(3000))
        ->call('saveAvatar')
        ->assertHasErrors(['avatar' => 'max']);
});

// ── LinkedAccounts ────────────────────────────────────────────────────────────

it('can unlink google account when user has a password', function () {
    $user = User::factory()->create([
        'google_id' => 'google-123',
        'password' => Hash::make('secret'),
    ]);

    Livewire::actingAs($user)
        ->test('profile.linked-accounts')
        ->call('unlink', 'google')
        ->assertHasNoErrors();

    expect($user->fresh()->google_id)->toBeNull();
});

it('can unlink facebook account when user has a password', function () {
    $user = User::factory()->create([
        'facebook_id' => 'fb-456',
        'password' => Hash::make('secret'),
    ]);

    Livewire::actingAs($user)
        ->test('profile.linked-accounts')
        ->call('unlink', 'facebook')
        ->assertHasNoErrors();

    expect($user->fresh()->facebook_id)->toBeNull();
});

it('cannot unlink when user has no password set', function () {
    $user = User::factory()->withoutPassword()->create([
        'google_id' => 'google-123',
    ]);

    Livewire::actingAs($user->fresh())
        ->test('profile.linked-accounts')
        ->call('unlink', 'google')
        ->assertHasErrors(['unlink']);

    expect($user->fresh()->google_id)->toBe('google-123');
});

it('rejects invalid provider in unlink', function () {
    $user = User::factory()->create(['password' => Hash::make('secret')]);

    Livewire::actingAs($user)
        ->test('profile.linked-accounts')
        ->call('unlink', 'twitter')
        ->assertStatus(422);
});

// ── UploadedDocumentsTab ──────────────────────────────────────────────────────

it('shows uploaded documents for the authenticated user', function () {
    $user = User::factory()->create();
    $doc = Document::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(UploadedDocumentsTab::class)
        ->assertSee($doc->title);
});

it('filters uploaded documents by search term', function () {
    $user = User::factory()->create();
    $match = Document::factory()->create(['user_id' => $user->id, 'title' => 'Posebna priprava za matematiko']);
    $other = Document::factory()->create(['user_id' => $user->id, 'title' => 'Slovenscina uvod']);

    Livewire::actingAs($user)
        ->test(UploadedDocumentsTab::class)
        ->set('search', 'matematiko')
        ->assertSee($match->title)
        ->assertDontSee($other->title);
});

it('can delete an uploaded document', function () {
    $user = User::factory()->create();
    $doc = Document::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(UploadedDocumentsTab::class)
        ->call('deleteDocument', $doc->id);

    $this->assertSoftDeleted('documents', ['id' => $doc->id]);
});

it('cannot delete another users document', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $doc = Document::factory()->create(['user_id' => $other->id]);

    Livewire::actingAs($user)
        ->test(UploadedDocumentsTab::class)
        ->call('deleteDocument', $doc->id)
        ->assertForbidden();
});

// ── DownloadedDocumentsTab ────────────────────────────────────────────────────

it('shows downloaded documents for the authenticated user', function () {
    $user = User::factory()->create();
    $doc = Document::factory()->create();
    DownloadRecord::factory()->create(['user_id' => $user->id, 'document_id' => $doc->id]);

    Livewire::actingAs($user)
        ->test(DownloadedDocumentsTab::class)
        ->assertSee($doc->title);
});

// ── SavedDocumentsTab ─────────────────────────────────────────────────────────

it('shows saved documents for the authenticated user', function () {
    $user = User::factory()->create();
    $doc = Document::factory()->create();
    $user->savedDocuments()->attach($doc->id);

    Livewire::actingAs($user)
        ->test(SavedDocumentsTab::class)
        ->assertSee($doc->title);
});
