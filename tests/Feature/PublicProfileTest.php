<?php

use App\Livewire\PublicUploadedDocuments;
use App\Models\Document;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Route access ──────────────────────────────────────────────────────────────

it('shows the public profile page to guests', function () {
    $user = User::factory()->create();

    $this->get(route('profile.show', $user))
        ->assertSuccessful();
});

it('shows the public profile page to authenticated users', function () {
    $viewer = User::factory()->create();
    $profileUser = User::factory()->create();

    $this->actingAs($viewer)
        ->get(route('profile.show', $profileUser))
        ->assertSuccessful();
});

it('returns 404 for a non-existent user slug', function () {
    $this->get('/profil/non-existent-slug')
        ->assertNotFound();
});

// ── Hero section content ──────────────────────────────────────────────────────

it('displays the user display name on the public profile', function () {
    $user = User::factory()->create(['display_name' => 'Ana Novak']);

    $this->get(route('profile.show', $user))
        ->assertSuccessful()
        ->assertSee('Ana Novak');
});

it('displays the member since date on the public profile', function () {
    $user = User::factory()->create();

    $this->get(route('profile.show', $user))
        ->assertSuccessful()
        ->assertSee('Član od');
});

it('shows the upload count on the public profile', function () {
    $user = User::factory()->create();
    Document::factory()->count(3)->create(['user_id' => $user->id]);

    $this->get(route('profile.show', $user))
        ->assertSuccessful()
        ->assertSee('Naloženih priprav');
});

it('does not show the email address on the public profile', function () {
    $user = User::factory()->create(['email' => 'secret@example.com']);

    $this->get(route('profile.show', $user))
        ->assertSuccessful()
        ->assertDontSee('secret@example.com');
});

// ── PublicUploadedDocuments Livewire component ────────────────────────────────

it('shows uploaded documents for the given user', function () {
    $user = User::factory()->create();
    $doc = Document::factory()->create(['user_id' => $user->id]);

    Livewire::test(PublicUploadedDocuments::class, ['userId' => $user->id])
        ->assertSee($doc->title);
});

it('does not show documents from other users', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $otherDoc = Document::factory()->create(['user_id' => $other->id]);

    Livewire::test(PublicUploadedDocuments::class, ['userId' => $user->id])
        ->assertDontSee($otherDoc->title);
});

it('shows an empty state when the user has no uploads', function () {
    $user = User::factory()->create();

    Livewire::test(PublicUploadedDocuments::class, ['userId' => $user->id])
        ->assertSee('Ta uporabnik še nima naloženih priprav');
});

it('paginates documents with 5 per page', function () {
    $user = User::factory()->create();

    // Create 6 docs with distinct timestamps so ordering is deterministic
    $docs = collect();
    for ($i = 0; $i < 6; $i++) {
        $docs->push(Document::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subMinutes($i),
        ]));
    }

    // The oldest document (last created, smallest created_at) should be on page 2
    $oldest = $docs->last();

    Livewire::test(PublicUploadedDocuments::class, ['userId' => $user->id])
        ->assertDontSee($oldest->title);
});

it('shows documents from the second page after navigating', function () {
    $user = User::factory()->create();

    // Create 6 docs with distinct timestamps so ordering is deterministic
    $docs = collect();
    for ($i = 0; $i < 6; $i++) {
        $docs->push(Document::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subMinutes($i),
        ]));
    }

    // The oldest document should appear on page 2
    $oldest = $docs->last();

    Livewire::test(PublicUploadedDocuments::class, ['userId' => $user->id])
        ->call('nextPage')
        ->assertSee($oldest->title);
});
