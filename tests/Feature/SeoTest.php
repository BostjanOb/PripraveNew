<?php

use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\User;
use App\Services\Browse\BrowseSearchInput;
use App\Services\Browse\BrowseSearchResult;
use App\Services\Browse\BrowseSearchService;
use App\Services\Documents\RelatedDocumentsSearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()->bind(BrowseSearchService::class, fn (): BrowseSearchService => new class implements BrowseSearchService
    {
        public function search(BrowseSearchInput $input): BrowseSearchResult
        {
            $query = Document::query()->with(['user', 'schoolType', 'grade', 'subject', 'category']);

            if ($input->q !== '') {
                $query->likeSearch($input->q);
            }

            if ($input->schoolTypeId !== null) {
                $query->where('school_type_id', $input->schoolTypeId);
            }

            if ($input->gradeId !== null) {
                $query->where('grade_id', $input->gradeId);
            }

            if ($input->subjectId !== null) {
                $query->where('subject_id', $input->subjectId);
            }

            if ($input->categoryIds !== []) {
                $query->whereIn('category_id', $input->categoryIds);
            }

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
                facetCounts: [],
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
    });

    app()->bind(RelatedDocumentsSearchService::class, fn (): RelatedDocumentsSearchService => new class implements RelatedDocumentsSearchService
    {
        public function search(Document $document, int $limit = 3): Collection
        {
            return collect();
        }
    });
});

it('renders seo metadata for public pages', function () {
    $document = Document::factory()->create([
        'title' => 'Osnovnošolska matematika',
        'description' => 'Podrobna priprava za matematiko v osnovni šoli.',
    ]);

    $activeProfile = User::factory()->create(['display_name' => 'Aktivna Avtorica']);
    Document::factory()->create(['user_id' => $activeProfile->id]);

    $emptyProfile = User::factory()->create(['display_name' => 'Brez Gradiv']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<meta name="description" content="Poiščite učne priprave, delovne liste in teste za predšolsko vzgojo, osnovno in srednjo šolo na Priprave.net.">', false)
        ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
        ->assertSee('<meta name="robots" content="index,follow">', false);

    $this->get(route('document.show', $document))
        ->assertOk()
        ->assertSee('<meta name="description" content="Podrobna priprava za matematiko v osnovni šoli.">', false)
        ->assertSee('<link rel="canonical" href="'.route('document.show', $document).'">', false)
        ->assertSee('<meta property="og:type" content="article">', false)
        ->assertSee('application/ld+json', false);

    $this->get(route('profile.show', $activeProfile))
        ->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false);

    $this->get(route('profile.show', $emptyProfile))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);
});

it('renders browse seo rules for base and filtered pages', function () {
    $this->get(route('browse'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false)
        ->assertSee('<link rel="canonical" href="'.route('browse').'">', false);

    $this->get(route('browse', ['q' => 'matematika']))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false)
        ->assertSee('<link rel="canonical" href="'.route('browse').'">', false);
});

it('renders noindex metadata for auth pages', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,nofollow">', false);
});

it('renders robots and sitemap xml responses', function () {
    $document = Document::factory()->create(['slug' => 'seo-test-document']);
    $activeProfile = User::factory()->create(['display_name' => 'Sitemap Author']);
    Document::factory()->create(['user_id' => $activeProfile->id]);
    $emptyProfile = User::factory()->create(['display_name' => 'No Uploads']);
    $directory = storage_path('framework/testing/sitemaps');
    $path = $directory.'/sitemap.xml';

    File::deleteDirectory($directory);

    expect(File::exists(public_path('robots.txt')))->toBeTrue()
        ->and(File::get(public_path('robots.txt')))->toContain('Sitemap:')
        ->and(File::get(public_path('robots.txt')))->toContain('/sitemap.xml');

    artisan('sitemap:generate', [
        '--path' => $path,
        '--max-tags' => 2,
    ])->assertSuccessful();

    $chunkFiles = collect(File::glob($directory.'/sitemap_*.xml'));

    expect(File::exists($path))->toBeTrue()
        ->and($chunkFiles->isNotEmpty())->toBeTrue();

    expect(File::get($path))
        ->toContain('sitemap_0.xml')
        ->toContain('.xml');

    $chunks = $chunkFiles
        ->map(fn (string $file): string => File::get($file))
        ->implode('');

    expect($chunks)
        ->toContain(route('document.show', $document))
        ->toContain(route('profile.show', $activeProfile))
        ->not->toContain(route('profile.show', $emptyProfile));
});

it('renders livewire browse pagination controls without crawlable anchors', function () {
    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $category = Category::factory()->create();

    Document::factory()->count(16)->create([
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    $this->get(route('browse'))
        ->assertOk()
        ->assertSee('wire:click="nextPage(\'page\')"', false)
        ->assertSee('wire:click="gotoPage(2, \'page\')"', false)
        ->assertDontSee('rel="next"', false)
        ->assertDontSee('href="'.route('browse', ['page' => 2]).'"', false);
});

it('shows the seo description backlog in priority order', function () {
    $highPriority = Document::factory()->create([
        'title' => 'Najprej',
        'description' => null,
        'downloads_count' => 150,
        'views_count' => 100,
    ]);

    $lowerPriority = Document::factory()->create([
        'title' => 'Kasneje',
        'description' => null,
        'downloads_count' => 50,
        'views_count' => 10,
    ]);

    artisan('seo:description-backfill', ['--limit' => 2])
        ->expectsOutputToContain($highPriority->title)
        ->expectsOutputToContain($lowerPriority->title)
        ->assertSuccessful();
});
