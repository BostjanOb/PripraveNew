<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class BrowseDocuments extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'stopnja')]
    public ?string $schoolTypeSlug = null;

    #[Url(as: 'razred')]
    public ?int $gradeId = null;

    #[Url(as: 'predmet')]
    public ?int $subjectId = null;

    /** @var list<int> */
    #[Url(as: 'tip')]
    public array $categoryIds = [];

    #[Url(as: 'razvrsti')]
    public string $sort = 'newest';

    public string $subjectSearch = '';

    public int $page = 1;

    private const ITEMS_PER_PAGE = 10;

    public function mount(): void
    {
        $this->normalizeSchoolTypeSlug();
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function setSchoolType(?string $slug): void
    {
        $this->schoolTypeSlug = $slug;
        $this->normalizeSchoolTypeSlug();
        $this->gradeId = null;
        $this->subjectId = null;
        $this->subjectSearch = '';
        $this->page = 1;
    }

    public function setGrade(?int $gradeId): void
    {
        $this->gradeId = $gradeId;
        $this->page = 1;
    }

    public function setSubject(?int $subjectId): void
    {
        $this->subjectId = $subjectId;
        $this->page = 1;
    }

    public function toggleCategory(int $categoryId): void
    {
        if (in_array($categoryId, $this->categoryIds)) {
            $this->categoryIds = array_values(array_diff($this->categoryIds, [$categoryId]));
        } else {
            $this->categoryIds[] = $categoryId;
        }

        $this->page = 1;
    }

    public function updatedSort(): void
    {
        $this->page = 1;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function clearAllFilters(): void
    {
        $this->reset(['search', 'schoolTypeSlug', 'gradeId', 'subjectId', 'categoryIds', 'sort', 'subjectSearch']);
        $this->page = 1;
    }

    public function removeFilter(string $filter): void
    {
        match ($filter) {
            'search' => $this->search = '',
            'schoolTypeSlug' => $this->setSchoolType(null),
            'gradeId' => $this->gradeId = null,
            'subjectId' => $this->subjectId = null,
            'categoryIds' => $this->categoryIds = [],
            'sort' => $this->sort = 'newest',
            default => null,
        };

        $this->page = 1;
    }

    public function render(): View
    {
        $schoolTypes = SchoolType::query()->orderBy('sort_order')->get();
        $categories = Category::query()->topLevel()->with('children')->orderBy('sort_order')->get();
        $allCategories = $categories->flatMap(fn (Category $c) => $c->children->isEmpty() ? collect([$c]) : $c->children);

        $schoolType = $this->schoolTypeSlug
            ? $schoolTypes->firstWhere('slug', $this->schoolTypeSlug)
            : null;
        $schoolTypeId = $schoolType?->id;

        $grades = Grade::query()->orderBy('sort_order')->get();

        $subjects = $schoolTypeId
            ? Subject::query()->where('school_type_id', $schoolTypeId)->orderBy('name')->get()
            : Subject::query()->orderBy('name')->get();

        if ($this->subjectSearch !== '') {
            $subjectSearchLower = mb_strtolower($this->subjectSearch);
            $subjects = $subjects->filter(
                fn (Subject $s) => str_contains(mb_strtolower($s->name), $subjectSearchLower)
            );
        }

        $isMeilisearch = config('scout.driver') === 'meilisearch';

        if ($isMeilisearch) {
            $result = $this->searchMeilisearch($schoolTypeId);
        } else {
            $result = $this->searchCollection($schoolTypeId);
        }

        $selectedCategories = $this->categoryIds !== []
            ? $allCategories->whereIn('id', $this->categoryIds)
            : collect();

        return view('livewire.browse-documents', [
            'documents' => $result['documents'],
            'totalHits' => $result['totalHits'],
            'totalPages' => $result['totalPages'],
            'currentPage' => $result['currentPage'],
            'facetCounts' => $result['facetCounts'],
            'hasFacetCounts' => $result['facetCounts'] !== [],
            'schoolTypes' => $schoolTypes,
            'grades' => $grades,
            'subjects' => $subjects,
            'allCategories' => $allCategories,
            'selectedSchoolType' => $schoolType,
            'selectedCategories' => $selectedCategories,
            'hasActiveFilters' => $this->hasActiveFilters(),
        ])->layout('components.layouts.app');
    }

    /**
     * @return array{documents: Collection, totalHits: int, totalPages: int, currentPage: int, facetCounts: array<string, array<int, int>>}
     */
    private function searchMeilisearch(?int $schoolTypeId): array
    {
        $filters = $this->buildFilterString($schoolTypeId);
        $meilisearchSort = $this->getMeilisearchSort();

        $raw = Document::search($this->search)
            ->options([
                'filter' => $filters ?: null,
                'sort' => $meilisearchSort,
                'facets' => ['school_type_id', 'grade_id', 'subject_id', 'category_id'],
                'hitsPerPage' => self::ITEMS_PER_PAGE,
                'page' => $this->page,
            ])
            ->raw();

        $ids = collect($raw['hits'] ?? [])->pluck('id');
        $documents = $ids->isNotEmpty()
            ? Document::query()
                ->with(['user', 'schoolType', 'grade', 'subject', 'category'])
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn (Document $doc) => $ids->search($doc->id))
                ->values()
            : collect();

        $facetCounts = $this->computeFacetCounts($schoolTypeId);

        return [
            'documents' => $documents,
            'totalHits' => $raw['totalHits'] ?? 0,
            'totalPages' => $raw['totalPages'] ?? 1,
            'currentPage' => (int) ($raw['page'] ?? 1),
            'facetCounts' => $facetCounts,
        ];
    }

    /**
     * Fallback for collection driver (tests).
     *
     * @return array{documents: Collection, totalHits: int, totalPages: int, currentPage: int, facetCounts: array<string, array<int, int>>}
     */
    private function searchCollection(?int $schoolTypeId): array
    {
        $query = Document::query()
            ->with(['user', 'schoolType', 'grade', 'subject', 'category']);

        if ($this->search !== '') {
            $query->likeSearch($this->search);
        }

        if ($schoolTypeId) {
            $query->where('school_type_id', $schoolTypeId);
        }

        if ($this->gradeId) {
            $query->where('grade_id', $this->gradeId);
        }

        if ($this->subjectId) {
            $query->where('subject_id', $this->subjectId);
        }

        if ($this->categoryIds !== []) {
            $query->whereIn('category_id', $this->categoryIds);
        }

        match ($this->sort) {
            'oldest' => $query->oldest(),
            'most-downloaded' => $query->orderByDesc('downloads_count'),
            'most-viewed' => $query->orderByDesc('views_count'),
            default => $query->latest(),
        };

        $totalHits = $query->count();
        $totalPages = max(1, (int) ceil($totalHits / self::ITEMS_PER_PAGE));
        $documents = $query->skip(($this->page - 1) * self::ITEMS_PER_PAGE)->take(self::ITEMS_PER_PAGE)->get();

        return [
            'documents' => $documents,
            'totalHits' => $totalHits,
            'totalPages' => $totalPages,
            'currentPage' => $this->page,
            'facetCounts' => [],
        ];
    }

    private function buildFilterString(?int $schoolTypeId): string
    {
        $filters = [];

        if ($schoolTypeId) {
            $filters[] = "school_type_id = {$schoolTypeId}";
        }

        if ($this->gradeId) {
            $filters[] = "grade_id = {$this->gradeId}";
        }

        if ($this->subjectId) {
            $filters[] = "subject_id = {$this->subjectId}";
        }

        if ($this->categoryIds !== []) {
            $ids = implode(', ', $this->categoryIds);
            $filters[] = "category_id IN [{$ids}]";
        }

        return implode(' AND ', $filters);
    }

    /**
     * Compute facet counts per filter section, excluding that section's own filter.
     *
     * @return array<string, array<int, int>>
     */
    private function computeFacetCounts(?int $schoolTypeId): array
    {
        $baseFilters = [];

        if ($schoolTypeId) {
            $baseFilters['school_type_id'] = "school_type_id = {$schoolTypeId}";
        }

        if ($this->gradeId) {
            $baseFilters['grade_id'] = "grade_id = {$this->gradeId}";
        }

        if ($this->subjectId) {
            $baseFilters['subject_id'] = "subject_id = {$this->subjectId}";
        }

        if ($this->categoryIds !== []) {
            $ids = implode(', ', $this->categoryIds);
            $baseFilters['category_id'] = "category_id IN [{$ids}]";
        }

        $counts = [];
        $facetKeys = ['school_type_id', 'grade_id', 'subject_id', 'category_id'];

        foreach ($facetKeys as $facetKey) {
            $filtersWithout = collect($baseFilters)->except($facetKey)->values()->implode(' AND ');

            $result = Document::search($this->search)
                ->options([
                    'filter' => $filtersWithout ?: null,
                    'facets' => [$facetKey],
                    'hitsPerPage' => 0,
                    'page' => 1,
                ])
                ->raw();

            $counts[$facetKey] = $result['facetDistribution'][$facetKey] ?? [];
        }

        return $counts;
    }

    /**
     * @return list<string>
     */
    private function getMeilisearchSort(): array
    {
        return match ($this->sort) {
            'oldest' => ['created_at_ts:asc'],
            'most-downloaded' => ['downloads_count:desc'],
            'most-viewed' => ['views_count:desc'],
            default => ['created_at_ts:desc'],
        };
    }

    private function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->schoolTypeSlug !== null
            || $this->gradeId !== null
            || $this->subjectId !== null
            || $this->categoryIds !== [];
    }

    /**
     * Normalize various slug formats (pv, os, ss, predskolska, osnovna, srednja).
     */
    private function normalizeSchoolTypeSlug(): void
    {
        if ($this->schoolTypeSlug === null) {
            return;
        }

        $mapping = [
            'predskolska' => 'pv',
            'osnovna' => 'os',
            'srednja' => 'ss',
        ];

        $this->schoolTypeSlug = $mapping[$this->schoolTypeSlug] ?? $this->schoolTypeSlug;
    }
}
