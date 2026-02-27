<?php

use App\Livewire\BrowseDocuments;
use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use App\Services\Browse\BrowseSearchInput;
use App\Services\Browse\BrowseSearchResult;
use App\Services\Browse\BrowseSearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    app()->bind(BrowseSearchService::class, FakeBrowseSearchService::class);
});

final class FakeBrowseSearchService implements BrowseSearchService
{
    public function search(BrowseSearchInput $input): BrowseSearchResult
    {
        $query = $this->baseQuery($input);
        $this->applySorting($query, $input->sort);

        $totalHits = $query->count();
        $totalPages = max(1, (int) ceil($totalHits / $input->hitsPerPage));
        $documents = $query
            ->skip(($input->page - 1) * $input->hitsPerPage)
            ->take($input->hitsPerPage)
            ->get();

        return new BrowseSearchResult(
            documents: $documents,
            totalHits: $totalHits,
            totalPages: $totalPages,
            currentPage: $input->page,
            facetCounts: $this->facetCounts($input),
        );
    }

    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'most-downloaded' => $query->orderByDesc('downloads_count'),
            'most-viewed' => $query->orderByDesc('views_count'),
            default => $query->latest(),
        };
    }

    /**
     * @return array<string, array<int, int>>
     */
    private function facetCounts(BrowseSearchInput $input): array
    {
        $counts = [];

        foreach (['school_type_id', 'grade_id', 'subject_id', 'category_id'] as $facetKey) {
            $query = $this->baseQuery($input, $this->isSelectedFacet($input, $facetKey) ? $facetKey : null);

            if ($facetKey !== 'school_type_id') {
                $query->whereNotNull($facetKey);
            }

            $counts[$facetKey] = $query
                ->selectRaw("{$facetKey}, COUNT(*) as aggregate")
                ->groupBy($facetKey)
                ->pluck('aggregate', $facetKey)
                ->map(fn (int|string $count): int => (int) $count)
                ->all();
        }

        return $counts;
    }

    private function isSelectedFacet(BrowseSearchInput $input, string $facetKey): bool
    {
        return match ($facetKey) {
            'school_type_id' => $input->schoolTypeId !== null,
            'grade_id' => $input->gradeId !== null,
            'subject_id' => $input->subjectId !== null,
            'category_id' => $input->categoryIds !== [],
            default => false,
        };
    }

    private function baseQuery(BrowseSearchInput $input, ?string $excludeFacet = null): Builder
    {
        $query = Document::query()->with(['user', 'schoolType', 'grade', 'subject', 'category']);

        if ($input->q !== '') {
            $query->likeSearch($input->q);
        }

        if ($excludeFacet !== 'school_type_id' && $input->schoolTypeId !== null) {
            $query->where('school_type_id', $input->schoolTypeId);
        }

        if ($excludeFacet !== 'grade_id' && $input->gradeId !== null) {
            $query->where('grade_id', $input->gradeId);
        }

        if ($excludeFacet !== 'subject_id' && $input->subjectId !== null) {
            $query->where('subject_id', $input->subjectId);
        }

        if ($excludeFacet !== 'category_id' && $input->categoryIds !== []) {
            $query->whereIn('category_id', $input->categoryIds);
        }

        return $query;
    }
}

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

it('uses alpine for subject filter search', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Angleščina']);
    $category = Category::factory()->create();

    Document::factory()->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->assertSeeHtml('x-model="query"')
        ->assertSeeHtml('data-subject-item')
        ->assertSeeHtml('x-show="matches($el)"')
        ->assertDontSeeHtml('wire:model.live="subjectSearch"');
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

it('filters by school type from stopnja query param using school type slug', function () {
    $pvSchoolType = SchoolType::factory()->create(['slug' => 'pv']);
    $osSchoolType = SchoolType::factory()->create(['slug' => 'os']);

    $pvGrade = Grade::factory()->create(['school_type_id' => $pvSchoolType->id]);
    $pvSubject = Subject::factory()->create(['school_type_id' => $pvSchoolType->id]);
    $osGrade = Grade::factory()->create(['school_type_id' => $osSchoolType->id]);
    $osSubject = Subject::factory()->create(['school_type_id' => $osSchoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->create([
        'title' => 'PV doc',
        'user_id' => User::factory(),
        'school_type_id' => $pvSchoolType->id,
        'grade_id' => $pvGrade->id,
        'subject_id' => $pvSubject->id,
        'category_id' => $category->id,
    ]);

    Document::factory()->create([
        'title' => 'OS doc',
        'user_id' => User::factory(),
        'school_type_id' => $osSchoolType->id,
        'grade_id' => $osGrade->id,
        'subject_id' => $osSubject->id,
        'category_id' => $category->id,
    ]);

    Livewire::withQueryParams(['stopnja' => 'pv'])
        ->test(BrowseDocuments::class)
        ->assertSee('PV doc')
        ->assertDontSee('OS doc');
});

it('shows facet counts while structured filters are active', function () {
    $schoolTypeOs = SchoolType::factory()->create(['slug' => 'os']);
    $schoolTypeSs = SchoolType::factory()->create(['slug' => 'ss']);
    $grade = Grade::factory()->create(['school_type_id' => $schoolTypeOs->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolTypeOs->id]);
    $category = Category::factory()->create();

    Document::factory()->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolTypeOs->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Document::factory()->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolTypeSs->id,
        'grade_id' => Grade::factory()->create(['school_type_id' => $schoolTypeSs->id])->id,
        'subject_id' => Subject::factory()->create(['school_type_id' => $schoolTypeSs->id])->id,
        'category_id' => Category::factory()->create()->id,
    ]);

    Livewire::withQueryParams(['stopnja' => 'os'])
        ->test(BrowseDocuments::class)
        ->assertViewHas('hasFacetCounts', true)
        ->assertViewHas('facetCounts', function (array $facetCounts) use ($schoolTypeOs): bool {
            return ($facetCounts['school_type_id'][$schoolTypeOs->id] ?? 0) > 0;
        });
});

it('hides grade and subject options with zero results', function () {
    $schoolType = SchoolType::factory()->create(['slug' => 'os']);

    $visibleGrade = Grade::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Vidni razred']);
    $hiddenGrade = Grade::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Skriti razred']);

    $visibleSubject = Subject::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Vidni predmet']);
    $hiddenSubject = Subject::factory()->create(['school_type_id' => $schoolType->id, 'name' => 'Skriti predmet']);

    $category = Category::factory()->create();

    Document::factory()->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $visibleGrade->id,
        'subject_id' => $visibleSubject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->assertSee('Vidni razred')
        ->assertDontSee('Skriti razred')
        ->assertSee('Vidni predmet')
        ->assertDontSee('Skriti predmet');
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

it('orders grades by school type sort order', function () {
    $schoolTypePv = SchoolType::factory()->create(['slug' => 'pv', 'sort_order' => 3]);
    $schoolTypeOs = SchoolType::factory()->create(['slug' => 'os', 'sort_order' => 1]);
    $schoolTypeSs = SchoolType::factory()->create(['slug' => 'ss', 'sort_order' => 2]);

    $pvGrade = Grade::factory()->create([
        'school_type_id' => $schoolTypePv->id,
        'name' => 'PV 1',
        'sort_order' => 1,
    ]);
    $osGrade = Grade::factory()->create([
        'school_type_id' => $schoolTypeOs->id,
        'name' => 'OS 1',
        'sort_order' => 1,
    ]);
    $ssGrade = Grade::factory()->create([
        'school_type_id' => $schoolTypeSs->id,
        'name' => 'SS 1',
        'sort_order' => 1,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->assertViewHas('grades', function (\Illuminate\Support\Collection $grades) use ($pvGrade, $osGrade, $ssGrade): bool {
            return $grades->pluck('id')->values()->all() === [
                $osGrade->id,
                $ssGrade->id,
                $pvGrade->id,
            ];
        });
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

it('paginates results at 20 per page', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->count(30)->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->assertViewHas('documents', function (mixed $documents): bool {
            return $documents instanceof LengthAwarePaginator
                && $documents->lastPage() === 2
                && $documents->currentPage() === 1;
        });
});

it('navigates to a specific page', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->create(['school_type_id' => $schoolType->id]);
    $category = Category::factory()->create();

    Document::factory()->count(50)->create([
        'user_id' => User::factory(),
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    Livewire::test(BrowseDocuments::class)
        ->call('setPage', 2)
        ->assertSet('paginators.page', 2);
});

// ── URL persistence ──────────────────────────────────────────────────────────

it('persists search in URL query string', function () {
    Livewire::test(BrowseDocuments::class)
        ->set('search', 'matematika')
        ->assertSet('search', 'matematika');
});
