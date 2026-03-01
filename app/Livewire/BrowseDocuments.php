<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Services\Browse\BrowseSearchInput;
use App\Services\Browse\BrowseSearchService;
use App\Support\SchoolTypeUiConfig;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BrowseDocuments extends Component
{
    use WithPagination;

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

    private const ITEMS_PER_PAGE = 15;

    public function mount(): void
    {
        $this->normalizeSchoolTypeSlug();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setSchoolType(?string $slug): void
    {
        $this->schoolTypeSlug = $slug;
        $this->normalizeSchoolTypeSlug();
        $this->gradeId = null;
        $this->subjectId = null;
        $this->resetPage();
    }

    public function setGrade(?int $gradeId): void
    {
        $this->gradeId = $gradeId;
        $this->resetPage();
    }

    public function setSubject(?int $subjectId): void
    {
        $this->subjectId = $subjectId;
        $this->resetPage();
    }

    public function toggleCategory(int $categoryId): void
    {
        if (in_array($categoryId, $this->categoryIds)) {
            $this->categoryIds = array_values(array_diff($this->categoryIds, [$categoryId]));
        } else {
            $this->categoryIds[] = $categoryId;
        }

        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function clearAllFilters(): void
    {
        $this->reset(['search', 'schoolTypeSlug', 'gradeId', 'subjectId', 'categoryIds', 'sort']);
        $this->resetPage();
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

        $this->resetPage();
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $schoolTypes = SchoolType::orderBy('sort_order')->get();
        $categories = Category::with('children')->topLevel()->orderBy('sort_order')->get();
        $allCategories = $categories->flatMap(fn (Category $c) => $c->children->isEmpty() ? collect([$c]) : $c->children);

        $schoolType = $this->resolveSchoolType($schoolTypes);
        $schoolTypeId = $schoolType?->id;

        $grades = Grade::select('grades.*')
            ->join('school_types', 'school_types.id', '=', 'grades.school_type_id')
            ->orderBy('school_types.sort_order')
            ->orderBy('grades.sort_order')
            ->orderBy('grades.name')
            ->get();

        $subjects = Subject::when($schoolTypeId, fn ($query) => $query->forSchoolType($schoolTypeId))
            ->orderBy('name')
            ->get();

        $result = $this->browseSearchService()->search(
            new BrowseSearchInput(
                q: $this->search,
                page: $this->getPage(),
                hitsPerPage: self::ITEMS_PER_PAGE,
                sort: $this->sort,
                schoolTypeId: $schoolTypeId,
                gradeId: $this->gradeId,
                subjectId: $this->subjectId,
                categoryIds: $this->categoryIds,
            )
        );

        $selectedCategories = $this->categoryIds !== []
            ? $allCategories->whereIn('id', $this->categoryIds)
            : collect();

        $documents = new LengthAwarePaginator(
            items: $result->documents,
            total: $result->totalHits,
            perPage: self::ITEMS_PER_PAGE,
            currentPage: $result->currentPage,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );

        return view('livewire.browse-documents', [
            'documents' => $documents,
            'facetCounts' => $result->facetCounts,
            'hasFacetCounts' => $result->facetCounts !== [],
            'schoolTypes' => $schoolTypes,
            'grades' => $grades,
            'subjects' => $subjects,
            'allCategories' => $allCategories,
            'selectedSchoolType' => $schoolType,
            'schoolTypeConfig' => SchoolTypeUiConfig::all(),
            'selectedCategories' => $selectedCategories,
            'hasActiveFilters' => $this->hasActiveFilters(),
        ]);
    }

    /**
     * Keep stopnja URL value aligned with school_types.slug format.
     */
    private function normalizeSchoolTypeSlug(): void
    {
        if ($this->schoolTypeSlug === null) {
            return;
        }

        $this->schoolTypeSlug = Str::lower(trim($this->schoolTypeSlug));
    }

    private function resolveSchoolType(Collection $schoolTypes): ?SchoolType
    {
        if ($this->schoolTypeSlug === null) {
            return null;
        }

        /** @var ?SchoolType */
        return $schoolTypes->first(
            fn (SchoolType $schoolType): bool => Str::lower($schoolType->slug) === $this->schoolTypeSlug
        );
    }

    private function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->schoolTypeSlug !== null
            || $this->gradeId !== null
            || $this->subjectId !== null
            || $this->categoryIds !== [];
    }

    private function browseSearchService(): BrowseSearchService
    {
        return app(BrowseSearchService::class);
    }
}
