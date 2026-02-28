<?php

use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Access control ──────────────────────────────────────────────────────────

it('redirects guests to login', function () {
    $this->get(route('document.create'))
        ->assertRedirect(route('login'));
});

it('redirects unverified users', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $this->actingAs($user)
        ->get(route('document.create'))
        ->assertRedirect(route('verification.notice'));
});

it('shows the form to authenticated verified users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('document.create'))
        ->assertSuccessful()
        ->assertSee('Dodajanje gradiva');
});

// ── Successful submission ───────────────────────────────────────────────────

it('creates a document with priprava category', function () {
    Storage::fake();

    $user = User::factory()->create();
    $priprava = Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Test priprava za slovenščino')
        ->set('topic', 'Branje')
        ->set('keywords', 'slovenščina, branje')
        ->set('description', 'Testni opis')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $document = Document::query()->where('title', 'Test priprava za slovenščino')->first();

    expect($document)->not->toBeNull()
        ->and($document->category_id)->toBe(1)
        ->and($document->school_type_id)->toBe($schoolType->id)
        ->and($document->grade_id)->toBe($grade->id)
        ->and($document->subject_id)->toBe($subject->id)
        ->and($document->user_id)->toBe($user->id)
        ->and($document->slug)->toBe('test-priprava-za-slovenscino');

    expect($document->files)->toHaveCount(1);

    $docFile = $document->files->first();
    expect($docFile->original_name)->toBe('test.pdf')
        ->and($docFile->extension)->toBe('pdf')
        ->and($docFile->storage_path)->toEndWith('files.zip');

    Storage::assertExists($docFile->storage_path);
});

it('creates a document with ostalo category', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $ostaloParent = Category::factory()->create(['id' => 2, 'name' => 'Ostalo', 'slug' => 'ostalo']);
    $subcategory = Category::factory()->create(['parent_id' => 2, 'name' => 'Učni list', 'slug' => 'ucni-list']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    $file = UploadedFile::fake()->create('dokument.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'ostalo')
        ->set('ostaloCategory', (string) $subcategory->id)
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Učni list za matematiko')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $document = Document::query()->where('title', 'Učni list za matematiko')->first();

    expect($document)->not->toBeNull()
        ->and($document->category_id)->toBe($subcategory->id);
});

it('creates a new subject via createSubject action', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectSearch', 'Novi predmet za test')
        ->call('createSubject')
        ->set('title', 'Priprava z novim predmetom')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $subject = Subject::query()->where('name', 'Novi predmet za test')->first();
    expect($subject)->not->toBeNull()
        ->and($subject->schoolTypes()->whereKey($schoolType->id)->exists())->toBeTrue();

    $document = Document::query()->where('title', 'Priprava z novim predmetom')->first();
    expect($document->subject_id)->toBe($subject->id);
});

// ── Validation ──────────────────────────────────────────────────────────────

it('requires title and files', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', '')
        ->set('files', [])
        ->call('submit')
        ->assertHasErrors(['title' => 'required', 'files' => 'required']);
});

it('requires ostaloCategory when categoryType is ostalo', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'ostalo')
        ->set('ostaloCategory', '')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Test')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasErrors(['ostaloCategory' => 'required_if']);
});

it('requires school type and grade', function () {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', '')
        ->set('gradeId', '')
        ->set('title', 'Test')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasErrors(['schoolTypeId' => 'required', 'gradeId' => 'required']);
});

it('rejects subject that is not linked to selected school type', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $otherSchoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($otherSchoolType)->create();

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Test')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasErrors(['subjectId']);
});

it('rejects files with invalid extensions', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    $file = UploadedFile::fake()->create('malware.exe', 1024);

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Test')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasErrors(['files.0']);
});

// ── Slug uniqueness ─────────────────────────────────────────────────────────

it('generates unique slugs', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    // Create existing document with slug
    Document::factory()->create(['slug' => 'isti-naslov', 'title' => 'Isti naslov']);

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('categoryType', 'priprava')
        ->set('schoolTypeId', (string) $schoolType->id)
        ->set('gradeId', (string) $grade->id)
        ->set('subjectId', (string) $subject->id)
        ->set('title', 'Isti naslov')
        ->set('files', [$file])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $documents = Document::query()->where('title', 'Isti naslov')->get();
    expect($documents)->toHaveCount(2);

    $slugs = $documents->pluck('slug')->toArray();
    expect($slugs)->toContain('isti-naslov')
        ->and($slugs)->toContain('isti-naslov-1');
});

// ── Reset form ──────────────────────────────────────────────────────────────

it('resets the form', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('title', 'Nekaj')
        ->set('submitted', true)
        ->call('resetForm')
        ->assertSet('title', '')
        ->assertSet('submitted', false)
        ->assertSet('categoryType', 'priprava')
        ->assertSet('files', []);
});

// ── Remove file ─────────────────────────────────────────────────────────────

it('removes a file from the list', function () {
    $user = User::factory()->create();

    $file1 = UploadedFile::fake()->create('a.pdf', 100, 'application/pdf');
    $file2 = UploadedFile::fake()->create('b.pdf', 100, 'application/pdf');

    $component = Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('files', [$file1, $file2]);

    expect($component->get('files'))->toHaveCount(2);

    $component->call('removeFile', 0);

    expect($component->get('files'))->toHaveCount(1);
});
