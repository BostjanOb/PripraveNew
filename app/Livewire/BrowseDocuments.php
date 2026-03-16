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

    /** @var Collection<int, SchoolType> */
    public Collection $schoolTypes;

    /** @var Collection<int, Grade> */
    public Collection $grades;

    /** @var Collection<int, Category> */
    public Collection $categories;

    /** @var array<string, array<string, mixed>> */
    public array $schoolTypeConfig = [];

    private const ITEMS_PER_PAGE = 15;

    public function mount(): void
    {
        $this->schoolTypes = SchoolType::orderBy('sort_order')->get();
        $this->grades = Grade::select('grades.*')
            ->join('school_types', 'school_types.id', '=', 'grades.school_type_id')
            ->orderBy('school_types.sort_order')
            ->orderBy('grades.sort_order')
            ->orderBy('grades.name')
            ->get();
        $this->categories = Category::with('children')->topLevel()->orderBy('sort_order')->get();
        $this->schoolTypeConfig = SchoolTypeUiConfig::all();
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

    public function render(): View
    {
        $allCategories = $this->categories->flatMap(
            fn (Category $c) => $c->children->isEmpty() ? collect([$c]) : $c->children
        );

        $schoolType = $this->resolveSchoolType();
        $schoolTypeId = $schoolType?->id;

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
            'schoolTypes' => $this->schoolTypes,
            'grades' => $this->grades,
            'subjects' => $subjects,
            'allCategories' => $allCategories,
            'selectedSchoolType' => $schoolType,
            'schoolTypeConfig' => $this->schoolTypeConfig,
            'selectedCategories' => $selectedCategories,
            'hasActiveFilters' => $this->hasActiveFilters(),
        ])
            ->layout('components.layouts.app', [
                'metaDescription' => $this->metaDescription($schoolType),
                'canonical' => route('browse'),
                'robots' => $this->isIndexableBrowsePage() ? 'index,follow' : 'noindex,follow',
            ])
            ->title($this->pageTitle($schoolType));
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

    private function resolveSchoolType(): ?SchoolType
    {
        if ($this->schoolTypeSlug === null) {
            return null;
        }

        /** @var ?SchoolType */
        return $this->schoolTypes->first(
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

    private function isIndexableBrowsePage(): bool
    {
        return $this->search === ''
            && $this->schoolTypeSlug === null
            && $this->gradeId === null
            && $this->subjectId === null
            && $this->categoryIds === []
            && $this->getPage() === 1;
    }

    private function pageTitle(?SchoolType $schoolType): string
    {
        if ($this->isIndexableBrowsePage()) {
            return 'Brskanje po pripravah | Priprave.net';
        }

        if ($this->search !== '') {
            return Str::limit("Rezultati iskanja za {$this->search} | Priprave.net", 60);
        }

        if ($schoolType !== null) {
            return "Brskanje po pripravah za {$schoolType->name} | Priprave.net";
        }

        return 'Filtrirane priprave | Priprave.net';
    }

    private function metaDescription(?SchoolType $schoolType): string
    {
        if ($this->isIndexableBrowsePage()) {
            return 'Brskajte po učnih pripravah, delovnih listih in testih za predšolsko vzgojo, osnovno in srednjo šolo na Priprave.net.';
        }

        $parts = collect([
            $this->search !== '' ? "Iskanje: {$this->search}" : null,
            $schoolType?->name,
            $this->grades->firstWhere('id', $this->gradeId)?->name,
            $this->subjectId ? Subject::find($this->subjectId)?->name : null,
        ])->filter()->implode(', ');

        return Str::limit("Filtrirani rezultati brskanja po pripravah. {$parts}.", 160);
    }
}
