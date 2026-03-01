<?php

use App\Models\Comment;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Rating;
use App\Models\Report;
use App\Models\ReportReason;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Document show page ───────────────────────────────────────────────────────

it('shows the document detail page', function () {
    $document = Document::factory()->create();
    DocumentFile::factory()->create(['document_id' => $document->id]);

    $this->get(route('document.show', $document))
        ->assertSuccessful()
        ->assertSee($document->title);
});

it('shows document metadata on the detail page', function () {
    $document = Document::factory()->create([
        'description' => 'Opis testnega dokumenta',
        'topic' => 'Testna tema',
    ]);

    $this->get(route('document.show', $document))
        ->assertSuccessful()
        ->assertSee('Opis testnega dokumenta')
        ->assertSee('Testna tema');
});

it('shows author name on the document page', function () {
    $user = User::factory()->create(['display_name' => 'TestAvtor']);
    $document = Document::factory()->create(['user_id' => $user->id]);

    $this->get(route('document.show', $document))
        ->assertSuccessful()
        ->assertSee('TestAvtor');
});

it('increments view count once per session', function () {
    $document = Document::factory()->create(['views_count' => 5]);

    $this->get(route('document.show', $document));
    $this->get(route('document.show', $document));

    expect($document->fresh()->views_count)->toBe(6);
});

it('returns 404 for nonexistent document slug', function () {
    $this->get('/gradivo/nonexistent-slug-12345')
        ->assertNotFound();
});

it('shows edit and delete buttons only for the document owner', function () {
    $owner = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $owner->id]);

    // Owner sees delete button
    $this->actingAs($owner)
        ->get(route('document.show', $document))
        ->assertSuccessful()
        ->assertSee('Izbriši');

    // Other user does not see delete button
    $other = User::factory()->create();
    $this->actingAs($other)
        ->get(route('document.show', $document))
        ->assertSuccessful()
        ->assertDontSee('Izbriši');
});

it('shows file listing on the document page', function () {
    $document = Document::factory()->create();
    $file = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'test-datoteka.pdf',
    ]);

    $this->get(route('document.show', $document))
        ->assertSuccessful()
        ->assertSee('test-datoteka.pdf');
});

// ── Download file ────────────────────────────────────────────────────────────

it('requires authentication to download a file', function () {
    $document = Document::factory()->create();
    $file = DocumentFile::factory()->create(['document_id' => $document->id]);

    $this->get(route('document.download.file', [$document, $file]))
        ->assertRedirect(route('login'));
});

it('allows authenticated verified users to download a file', function () {
    Storage::fake();

    $document = Document::factory()->create(['downloads_count' => 0]);
    $file = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'storage_path' => 'documents/test.pdf',
        'original_name' => 'test.pdf',
    ]);

    Storage::put('documents/test.pdf', 'fake content');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('document.download.file', [$document, $file]))
        ->assertSuccessful();

    expect($document->fresh()->downloads_count)->toBe(1);
});

it('returns 404 when downloading a file that does not belong to the document', function () {
    $document = Document::factory()->create();
    $otherDocument = Document::factory()->create();
    $file = DocumentFile::factory()->create(['document_id' => $otherDocument->id]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('document.download.file', [$document, $file]))
        ->assertNotFound();
});

// ── Download ZIP ─────────────────────────────────────────────────────────────

it('requires authentication to download a ZIP', function () {
    $document = Document::factory()->create();

    $this->get(route('document.download.zip', $document))
        ->assertRedirect(route('login'));
});

it('allows authenticated verified users to download a ZIP', function () {
    Storage::fake();

    $document = Document::factory()->create(['downloads_count' => 0]);
    $file = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'storage_path' => 'documents/test.pdf',
        'original_name' => 'test.pdf',
    ]);

    Storage::put('documents/test.pdf', 'fake content');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('document.download.zip', $document))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/zip');
});

it('returns 404 when downloading ZIP for document with no files', function () {
    $document = Document::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('document.download.zip', $document))
        ->assertNotFound();
});

// ── Delete document ──────────────────────────────────────────────────────────

it('requires authentication to delete a document', function () {
    $document = Document::factory()->create();

    $this->delete(route('document.destroy', $document))
        ->assertRedirect(route('login'));
});

it('allows the owner to soft-delete a document', function () {
    $owner = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($owner)
        ->delete(route('document.destroy', $document))
        ->assertRedirect(route('profile'));

    $this->assertSoftDeleted('documents', ['id' => $document->id]);
});

it('forbids non-owner from deleting a document', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->delete(route('document.destroy', $document))
        ->assertForbidden();

    expect($document->fresh()->deleted_at)->toBeNull();
});

it('allows admin to soft-delete a non-owned document', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $document = Document::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($admin)
        ->delete(route('document.destroy', $document))
        ->assertRedirect(route('profile'));

    $this->assertSoftDeleted('documents', ['id' => $document->id]);
});

// ── Save button Livewire component ──────────────────────────────────────────

it('can save a document', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    Livewire::actingAs($user)
        ->test('document.save-button', ['document' => $document, 'isSaved' => false])
        ->call('toggle')
        ->assertSet('isSaved', true)
        ->assertDispatched('save-toggled');

    expect($user->savedDocuments()->where('document_id', $document->id)->exists())->toBeTrue();
});

it('can unsave a previously saved document', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $user->savedDocuments()->attach($document->id);

    Livewire::actingAs($user)
        ->test('document.save-button', ['document' => $document, 'isSaved' => true])
        ->call('toggle')
        ->assertSet('isSaved', false)
        ->assertDispatched('save-toggled');

    expect($user->savedDocuments()->where('document_id', $document->id)->exists())->toBeFalse();
});

it('does nothing when guest toggles save', function () {
    $document = Document::factory()->create();

    Livewire::test('document.save-button', ['document' => $document, 'isSaved' => false])
        ->call('toggle')
        ->assertSet('isSaved', false);
});

// ── Rating widget Livewire component ────────────────────────────────────────

it('can rate a document', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    Livewire::actingAs($user)
        ->test('document.rating-widget', ['document' => $document])
        ->call('rate', 4)
        ->assertSet('userRating', 4);

    expect(Rating::where('document_id', $document->id)->where('user_id', $user->id)->value('rating'))->toBe(4);
    expect($document->fresh()->rating_count)->toBe(1);
});

it('can update an existing rating', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    Rating::factory()->create(['document_id' => $document->id, 'user_id' => $user->id, 'rating' => 3]);
    $document->recalculateRating();

    Livewire::actingAs($user)
        ->test('document.rating-widget', ['document' => $document->fresh(), 'userRating' => 3])
        ->call('rate', 5)
        ->assertSet('userRating', 5);

    expect(Rating::where('document_id', $document->id)->where('user_id', $user->id)->value('rating'))->toBe(5);
});

it('rejects invalid star values', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    Livewire::actingAs($user)
        ->test('document.rating-widget', ['document' => $document])
        ->call('rate', 0)
        ->assertSet('userRating', null);

    Livewire::actingAs($user)
        ->test('document.rating-widget', ['document' => $document])
        ->call('rate', 6)
        ->assertSet('userRating', null);

    expect(Rating::where('document_id', $document->id)->count())->toBe(0);
});

it('does nothing when guest rates a document', function () {
    $document = Document::factory()->create();

    Livewire::test('document.rating-widget', ['document' => $document])
        ->call('rate', 4)
        ->assertSet('userRating', null);

    expect(Rating::where('document_id', $document->id)->count())->toBe(0);
});

// ── Comment section Livewire component ──────────────────────────────────────

it('can add a comment', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::actingAs($user)
        ->test('document.comment-section', ['document' => $document])
        ->set('text', 'Odlicna priprava!')
        ->call('addComment')
        ->assertHasNoErrors()
        ->assertSet('text', '');

    expect(Comment::where('document_id', $document->id)->where('user_id', $user->id)->exists())->toBeTrue();
    expect(Comment::where('document_id', $document->id)->first()->text)->toBe('Odlicna priprava!');
});

it('validates comment text is required', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::actingAs($user)
        ->test('document.comment-section', ['document' => $document])
        ->set('text', '')
        ->call('addComment')
        ->assertHasErrors(['text' => 'required']);
});

it('validates comment text minimum length', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::actingAs($user)
        ->test('document.comment-section', ['document' => $document])
        ->set('text', 'A')
        ->call('addComment')
        ->assertHasErrors(['text' => 'min']);
});

it('can delete own comment', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $comment = Comment::factory()->create([
        'document_id' => $document->id,
        'user_id' => $user->id,
        'text' => 'Moj komentar',
    ]);
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::actingAs($user)
        ->test('document.comment-section', ['document' => $document])
        ->call('deleteComment', $comment->id);

    expect(Comment::find($comment->id))->toBeNull();
});

it('allows admin to delete another users comment', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $document = Document::factory()->create();
    $comment = Comment::factory()->create([
        'document_id' => $document->id,
        'user_id' => $owner->id,
        'text' => 'Komentar lastnika',
    ]);
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::actingAs($admin)
        ->test('document.comment-section', ['document' => $document])
        ->call('deleteComment', $comment->id);

    expect(Comment::find($comment->id))->toBeNull();
});

it('cannot delete another users comment', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $document = Document::factory()->create();
    $comment = Comment::factory()->create([
        'document_id' => $document->id,
        'user_id' => $other->id,
        'text' => 'Tuj komentar',
    ]);
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::actingAs($user)
        ->test('document.comment-section', ['document' => $document])
        ->call('deleteComment', $comment->id)
        ->assertForbidden();

    expect(Comment::find($comment->id))->not->toBeNull();
});

it('does nothing when guest adds a comment', function () {
    $document = Document::factory()->create();
    $document->load(['comments' => fn ($q) => $q->with('user')->latest()]);

    Livewire::test('document.comment-section', ['document' => $document])
        ->set('text', 'Guest komentar')
        ->call('addComment');

    expect(Comment::where('document_id', $document->id)->count())->toBe(0);
});

// ── Report modal Livewire component ─────────────────────────────────────────

it('can submit a report', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $reason = ReportReason::factory()->create();

    Livewire::actingAs($user)
        ->test('document.report-modal', ['document' => $document])
        ->set('reportReasonId', $reason->id)
        ->set('message', 'Neprimerna vsebina')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    expect(Report::where('document_id', $document->id)->where('user_id', $user->id)->exists())->toBeTrue();
});

it('validates report reason is required', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    Livewire::actingAs($user)
        ->test('document.report-modal', ['document' => $document])
        ->set('reportReasonId', null)
        ->call('submit')
        ->assertHasErrors(['reportReasonId' => 'required']);
});

it('validates report reason exists in database', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();

    Livewire::actingAs($user)
        ->test('document.report-modal', ['document' => $document])
        ->set('reportReasonId', 99999)
        ->call('submit')
        ->assertHasErrors(['reportReasonId' => 'exists']);
});

it('can reset the report form', function () {
    $user = User::factory()->create();
    $document = Document::factory()->create();
    $reason = ReportReason::factory()->create();

    Livewire::actingAs($user)
        ->test('document.report-modal', ['document' => $document])
        ->set('reportReasonId', $reason->id)
        ->set('message', 'Nekaj')
        ->call('submit')
        ->assertSet('submitted', true)
        ->call('resetForm')
        ->assertSet('submitted', false)
        ->assertSet('reportReasonId', null)
        ->assertSet('message', '');
});

it('does nothing when guest submits a report', function () {
    $document = Document::factory()->create();
    $reason = ReportReason::factory()->create();

    Livewire::test('document.report-modal', ['document' => $document])
        ->set('reportReasonId', $reason->id)
        ->call('submit')
        ->assertSet('submitted', false);

    expect(Report::where('document_id', $document->id)->count())->toBe(0);
});
