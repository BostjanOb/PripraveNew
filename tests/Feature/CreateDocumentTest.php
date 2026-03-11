<?php

use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function storeDocumentZip(int $documentId, array $files): void
{
    $tempPath = tempnam(sys_get_temp_dir(), 'document_test_zip_');
    $zip = new \ZipArchive;
    $zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

    foreach ($files as $name => $content) {
        $zip->addFromString($name, $content);
    }

    $zip->close();

    Storage::put("documents/{$documentId}/files.zip", file_get_contents($tempPath));
    unlink($tempPath);
}

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
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', 'Test priprava za slovenščino')
        ->set('form.topic', 'Branje')
        ->set('form.keywords', 'slovenščina, branje')
        ->set('form.description', 'Testni opis')
        ->set('form.files', [$file])
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
        ->set('form.categoryType', 'ostalo')
        ->set('form.ostaloCategory', (string) $subcategory->id)
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', 'Učni list za matematiko')
        ->set('form.files', [$file])
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
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectSearch', 'Novi predmet za test')
        ->call('createSubject')
        ->set('form.title', 'Priprava z novim predmetom')
        ->set('form.files', [$file])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $subject = Subject::query()->where('name', 'Novi predmet za test')->first();
    expect($subject)->not->toBeNull()
        ->and($subject->schoolTypes()->whereKey($schoolType->id)->exists())->toBeTrue();

    $document = Document::query()->where('title', 'Priprava z novim predmetom')->first();
    expect($document->subject_id)->toBe($subject->id);
});

it('populates document fields when editing', function () {
    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    Category::factory()->create(['id' => 2, 'name' => 'Ostalo', 'slug' => 'ostalo']);
    $subcategory = Category::factory()->create(['parent_id' => 2, 'name' => 'Učni list', 'slug' => 'ucni-list']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $subject->schoolTypes()->syncWithoutDetaching([$schoolType->id]);

    $document = Document::factory()->create([
        'user_id' => $user->id,
        'category_id' => $subcategory->id,
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'title' => 'Obstoječe gradivo',
        'topic' => 'Tematika',
        'keywords' => 'ena, dve',
        'description' => 'Opis gradiva',
    ]);

    DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'obstojeci.pdf',
        'storage_path' => "documents/{$document->id}/files.zip",
        'extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
    ]);

    Livewire::actingAs($user)
        ->withQueryParams(['uredi' => $document->id])
        ->test(\App\Livewire\CreateDocument::class)
        ->assertSet('editingDocumentId', $document->id)
        ->assertSet('form.categoryType', 'ostalo')
        ->assertSet('form.ostaloCategory', (string) $subcategory->id)
        ->assertSet('form.schoolTypeId', (string) $schoolType->id)
        ->assertSet('form.gradeId', (string) $grade->id)
        ->assertSet('form.subjectId', (string) $subject->id)
        ->assertSet('form.title', 'Obstoječe gradivo')
        ->assertSet('form.topic', 'Tematika')
        ->assertSet('form.keywords', 'ena, dve')
        ->assertSet('form.description', 'Opis gradiva')
        ->assertSet('form.existingFiles.0.name', 'obstojeci.pdf');
});

it('updates an existing document without requiring new files', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    Category::factory()->create(['id' => 2, 'name' => 'Ostalo', 'slug' => 'ostalo']);
    $subcategory = Category::factory()->create(['parent_id' => 2, 'name' => 'Učni list', 'slug' => 'ucni-list']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $subject->schoolTypes()->syncWithoutDetaching([$schoolType->id]);

    $document = Document::factory()->create([
        'user_id' => $user->id,
        'category_id' => $subcategory->id,
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'title' => 'Stari naslov',
        'topic' => 'Stara tema',
        'keywords' => 'staro',
        'description' => 'Stari opis',
    ]);

    Storage::put("documents/{$document->id}/files.zip", 'existing-file');

    DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'obstojeci.pdf',
        'storage_path' => "documents/{$document->id}/files.zip",
        'extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
    ]);

    Livewire::actingAs($user)
        ->withQueryParams(['uredi' => $document->id])
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.categoryType', 'priprava')
        ->set('form.ostaloCategory', '')
        ->set('form.title', 'Posodobljen naslov')
        ->set('form.topic', 'Nova tema')
        ->set('form.keywords', 'novo, kljucno')
        ->set('form.description', 'Posodobljen opis')
        ->set('form.files', [])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    expect(Document::count())->toBe(1);

    $document->refresh()->load('files');

    expect($document->category_id)->toBe(1)
        ->and($document->title)->toBe('Posodobljen naslov')
        ->and($document->topic)->toBe('Nova tema')
        ->and($document->keywords)->toBe('novo, kljucno')
        ->and($document->description)->toBe('Posodobljen opis');

    expect($document->files)->toHaveCount(1)
        ->and($document->files->first()->original_name)->toBe('obstojeci.pdf');

    Storage::assertExists("documents/{$document->id}/files.zip");
});

it('appends new files to existing document files during editing', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $subject->schoolTypes()->syncWithoutDetaching([$schoolType->id]);

    $document = Document::factory()->create([
        'user_id' => $user->id,
        'category_id' => 1,
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
    ]);

    storeDocumentZip($document->id, [
        'old.pdf' => 'old file content',
    ]);

    DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'old.pdf',
        'storage_path' => "documents/{$document->id}/files.zip",
        'extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
    ]);

    $newFile = UploadedFile::fake()->create('novo.pdf', 2048, 'application/pdf');

    Livewire::actingAs($user)
        ->withQueryParams(['uredi' => $document->id])
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.title', 'Gradivo z novo datoteko')
        ->set('form.files', [$newFile])
        ->call('submit')
        ->assertHasNoErrors();

    $document->refresh()->load('files');

    expect($document->files)->toHaveCount(2);
    expect($document->files->pluck('original_name')->all())
        ->toContain('old.pdf')
        ->toContain('novo.pdf');

    Storage::assertExists("documents/{$document->id}/files.zip");
});

it('removes selected existing files during editing', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $subject->schoolTypes()->syncWithoutDetaching([$schoolType->id]);

    $document = Document::factory()->create([
        'user_id' => $user->id,
        'category_id' => 1,
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
    ]);

    storeDocumentZip($document->id, [
        'first.pdf' => 'first file content',
        'second.pdf' => 'second file content',
    ]);

    $firstFile = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'first.pdf',
        'storage_path' => "documents/{$document->id}/files.zip",
        'extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
    ]);

    $secondFile = DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'second.pdf',
        'storage_path' => "documents/{$document->id}/files.zip",
        'extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
    ]);

    Livewire::actingAs($user)
        ->withQueryParams(['uredi' => $document->id])
        ->test(\App\Livewire\CreateDocument::class)
        ->call('removeExistingFile', $firstFile->id)
        ->call('submit')
        ->assertHasNoErrors();

    $document->refresh()->load('files');

    expect($document->files)->toHaveCount(1)
        ->and($document->files->first()->original_name)->toBe('second.pdf')
        ->and(DocumentFile::withTrashed()->find($firstFile->id)?->trashed())->toBeTrue()
        ->and(DocumentFile::find($secondFile->id))->not->toBeNull();

    Storage::assertExists("documents/{$document->id}/files.zip");
});

it('requires at least one file to remain when editing', function () {
    Storage::fake();

    $user = User::factory()->create();
    Category::factory()->create(['id' => 1, 'name' => 'Priprava', 'slug' => 'priprava']);
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $subject->schoolTypes()->syncWithoutDetaching([$schoolType->id]);

    $document = Document::factory()->create([
        'user_id' => $user->id,
        'category_id' => 1,
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
    ]);

    DocumentFile::factory()->create([
        'document_id' => $document->id,
        'original_name' => 'only.pdf',
        'storage_path' => "documents/{$document->id}/files.zip",
        'extension' => 'pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024,
    ]);

    Livewire::actingAs($user)
        ->withQueryParams(['uredi' => $document->id])
        ->test(\App\Livewire\CreateDocument::class)
        ->call('removeExistingFile', $document->files()->firstOrFail()->id)
        ->call('submit')
        ->assertHasErrors(['form.files']);
});

it('forbids editing documents owned by another user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $document = Document::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($otherUser)
        ->get(route('document.create', ['uredi' => $document->id]))
        ->assertForbidden();
});

// ── Validation ──────────────────────────────────────────────────────────────

it('requires title and files', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', '')
        ->set('form.files', [])
        ->call('submit')
        ->assertHasErrors(['form.title' => 'required', 'form.files' => 'required']);
});

it('requires ostaloCategory when categoryType is ostalo', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.categoryType', 'ostalo')
        ->set('form.ostaloCategory', '')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', 'Test')
        ->set('form.files', [$file])
        ->call('submit')
        ->assertHasErrors(['form.ostaloCategory' => 'required_if']);
});

it('requires school type and grade', function () {
    $user = User::factory()->create();

    $file = UploadedFile::fake()->create('test.pdf', 1024, 'application/pdf');

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', '')
        ->set('form.gradeId', '')
        ->set('form.title', 'Test')
        ->set('form.files', [$file])
        ->call('submit')
        ->assertHasErrors(['form.schoolTypeId' => 'required', 'form.gradeId' => 'required']);
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
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', 'Test')
        ->set('form.files', [$file])
        ->call('submit')
        ->assertHasErrors(['form.subjectId']);
});

it('rejects files with invalid extensions', function () {
    $user = User::factory()->create();
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();

    $file = UploadedFile::fake()->create('malware.exe', 1024);

    Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', 'Test')
        ->set('form.files', [$file])
        ->call('submit')
        ->assertHasErrors(['form.files.0']);
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
        ->set('form.categoryType', 'priprava')
        ->set('form.schoolTypeId', (string) $schoolType->id)
        ->set('form.gradeId', (string) $grade->id)
        ->set('form.subjectId', (string) $subject->id)
        ->set('form.title', 'Isti naslov')
        ->set('form.files', [$file])
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
        ->set('form.title', 'Nekaj')
        ->set('submitted', true)
        ->call('resetForm')
        ->assertSet('form.title', '')
        ->assertSet('submitted', false)
        ->assertSet('form.categoryType', 'priprava')
        ->assertSet('form.files', []);
});

// ── Remove file ─────────────────────────────────────────────────────────────

it('removes a file from the list', function () {
    $user = User::factory()->create();

    $file1 = UploadedFile::fake()->create('a.pdf', 100, 'application/pdf');
    $file2 = UploadedFile::fake()->create('b.pdf', 100, 'application/pdf');

    $component = Livewire::actingAs($user)
        ->test(\App\Livewire\CreateDocument::class)
        ->set('form.files', [$file1, $file2]);

    expect($component->get('form.files'))->toHaveCount(2);

    $component->call('removeFile', 0);

    expect($component->get('form.files'))->toHaveCount(1);
});
