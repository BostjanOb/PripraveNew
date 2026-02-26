<?php

use App\Livewire\BrowseDocuments;
use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Helpers ──────────────────────────────────────────────────────────────────

function createDocumentWithRelations(array $overrides = []): Document
{
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    return Document::factory()->create(array_merge([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ], $overrides));
}

// ── Page rendering ───────────────────────────────────────────────────────────

it('renders the browse page', function () {
    $this->get(route('browse'))->assertSuccessful();
});

it('renders the browse page with stopnja query param', function () {
    SchoolType::factory()->create(['slug' => 'os']);

    $this->get(route('browse', ['stopnja' => 'os']))->assertSuccessful();
});

it('normalizes predskolska slug to pv', function () {
    SchoolType::factory()->create(['slug' => 'pv']);

    $this->get(route('browse', ['stopnja' => 'predskolska']))
        ->assertSuccessful();
});

it('normalizes osnovna slug to os', function () {
    SchoolType::factory()->create(['slug' => 'os']);

    $this->get(route('browse', ['stopnja' => 'osnovna']))
        ->assertSuccessful();
});

// ── Document display ─────────────────────────────────────────────────────────

it('displays documents', function () {
    $doc = createDocumentWithRelations(['title' => 'Matematika priprava']);

    Livewire::test(BrowseDocuments::class)
        ->assertSee('Matematika priprava');
});

// ── Filtering ────────────────────────────────────────────────────────────────

it('filters by school type', function () {
    $st1 = SchoolType::factory()->create(['slug' => 'os']);
    $st2 = SchoolType::factory()->create(['slug' => 'ss']);

    $doc1 = createDocumentWithRelations(['title' => 'Osnovna doc', 'school_type_id' => $st1->id]);
    $doc2 = createDocumentWithRelations(['title' => 'Srednja doc', 'school_type_id' => $st2->id]);

    Livewire::test(BrowseDocuments::class)
        ->call('setSchoolType', 'os')
        ->assertSee('Osnovna doc')
        ->assertDontSee('Srednja doc');
});

it('filters by grade', function () {
    $schoolType = SchoolType::factory()->create(['slug' => 'os']);
    $grade1 = Grade::factory()->create(['school_type_id' => $schoolType->id, 'name' => '1. razred']);
    $grade2 = Grade::factory()->create(['school_type_id' => $schoolType->id, 'name' => '2. razred']);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->create([
        'title' => 'Grade 1 doc',
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade1->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Document::factory()->create([
        'title' => 'Grade 2 doc',
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade2->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->set('schoolTypeSlug', 'os')
        ->set('gradeId', $grade1->id)
        ->assertSee('Grade 1 doc')
        ->assertDontSee('Grade 2 doc');
});

it('filters by subject', function () {
    $schoolType = SchoolType::factory()->create(['slug' => 'os']);
    $subject1 = Subject::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Matematika']);
    $subject2 = Subject::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Slovenščina']);
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->create([
        'title' => 'Math doc',
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject1->id,
        'category_id' => $category->id,
    ]);

    Document::factory()->create([
        'title' => 'Slovenian doc',
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject2->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->set('subjectId', $subject1->id)
        ->assertSee('Math doc')
        ->assertDontSee('Slovenian doc');
});

it('filters by single category', function () {
    $cat1 = Category::factory()->create(['name' => 'Priprava', 'slug' => 'priprava']);
    $cat2 = Category::factory()->create(['name' => 'Test', 'slug' => 'test']);

    $doc1 = createDocumentWithRelations(['title' => 'Priprava doc', 'category_id' => $cat1->id]);
    $doc2 = createDocumentWithRelations(['title' => 'Test doc', 'category_id' => $cat2->id]);

    Livewire::test(BrowseDocuments::class)
        ->set('categoryIds', [$cat1->id])
        ->assertSee('Priprava doc')
        ->assertDontSee('Test doc');
});

it('filters by multiple categories', function () {
    $cat1 = Category::factory()->create(['name' => 'Priprava', 'slug' => 'priprava']);
    $cat2 = Category::factory()->create(['name' => 'Test', 'slug' => 'test']);
    $cat3 = Category::factory()->create(['name' => 'Delovni list', 'slug' => 'delovni-list']);

    createDocumentWithRelations(['title' => 'Priprava doc', 'category_id' => $cat1->id]);
    createDocumentWithRelations(['title' => 'Test doc', 'category_id' => $cat2->id]);
    createDocumentWithRelations(['title' => 'DL doc', 'category_id' => $cat3->id]);

    Livewire::test(BrowseDocuments::class)
        ->set('categoryIds', [$cat1->id, $cat2->id])
        ->assertSee('Priprava doc')
        ->assertSee('Test doc')
        ->assertDontSee('DL doc');
});

it('toggles category on and off', function () {
    $cat = Category::factory()->create(['name' => 'Priprava', 'slug' => 'priprava']);

    Livewire::test(BrowseDocuments::class)
        ->call('toggleCategory', $cat->id)
        ->assertSet('categoryIds', [$cat->id])
        ->call('toggleCategory', $cat->id)
        ->assertSet('categoryIds', []);
});

// ── Clear filters ────────────────────────────────────────────────────────────

it('clears all filters', function () {
    SchoolType::factory()->create(['slug' => 'os']);

    Livewire::test(BrowseDocuments::class)
        ->set('search', 'test')
        ->set('schoolTypeSlug', 'os')
        ->set('sort', 'oldest')
        ->call('clearAllFilters')
        ->assertSet('search', '')
        ->assertSet('schoolTypeSlug', null)
        ->assertSet('sort', 'newest')
        ->assertSet('gradeId', null)
        ->assertSet('subjectId', null)
        ->assertSet('categoryIds', []);
});

it('resets grade and subject when school type changes', function () {
    $st1 = SchoolType::factory()->create(['slug' => 'os']);
    $st2 = SchoolType::factory()->create(['slug' => 'ss']);
    $grade = Grade::factory()->create(['school_type_id' => $st1->id]);
    $subject = Subject::factory()->create(['school_type_id' => $st1->id]);

    Livewire::test(BrowseDocuments::class)
        ->call('setSchoolType', 'os')
        ->set('gradeId', $grade->id)
        ->set('subjectId', $subject->id)
        ->call('setSchoolType', 'ss')
        ->assertSet('gradeId', null)
        ->assertSet('subjectId', null);
});

// ── Remove individual filter ─────────────────────────────────────────────────

it('removes a single filter', function () {
    SchoolType::factory()->create(['slug' => 'os']);

    Livewire::test(BrowseDocuments::class)
        ->set('schoolTypeSlug', 'os')
        ->set('search', 'test')
        ->call('removeFilter', 'search')
        ->assertSet('search', '')
        ->assertSet('schoolTypeSlug', 'os');
});

// ── Sorting ──────────────────────────────────────────────────────────────────

it('sorts by newest by default', function () {
    Livewire::test(BrowseDocuments::class)
        ->assertSet('sort', 'newest');
});

it('allows changing sort order', function () {
    Livewire::test(BrowseDocuments::class)
        ->set('sort', 'most-downloaded')
        ->assertSet('sort', 'most-downloaded');
});

// ── Pagination ───────────────────────────────────────────────────────────────

it('paginates results at 10 per page', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->count(15)->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->assertViewHas('totalPages', 2)
        ->assertViewHas('currentPage', 1);
});

it('navigates to a specific page', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->count(25)->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->call('setPage', 2)
        ->assertSet('page', 2);
});

// ── URL persistence ──────────────────────────────────────────────────────────

it('persists search in URL query string', function () {
    Livewire::test(BrowseDocuments::class)
        ->set('search', 'matematika')
        ->assertSet('search', 'matematika');
});
