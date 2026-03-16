<?php

use App\Models\Category;
use App\Models\Document;
use App\Models\Grade;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Services\Documents\MeilisearchRelatedDocumentsSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Meilisearch\Client;
use Meilisearch\Contracts\MultiSearchFederation;
use Meilisearch\Contracts\SearchQuery;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('builds meilisearch similarity queries and hydrates unique related documents in hit order', function () {
    Cache::flush();

    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $category = Category::factory()->create();

    $document = Document::factory()->create([
        'title' => 'Ulomki in decimala',
        'topic' => 'Seštevanje ulomkov',
        'keywords' => 'ulomki decimala matematika',
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    $firstRelatedDocument = Document::factory()->create([
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    $secondRelatedDocument = Document::factory()->create([
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    $client = new FakeMeilisearchClient([
        'results' => [
            [
                'hits' => [
                    ['id' => $document->id],
                    ['id' => $firstRelatedDocument->id],
                    ['id' => $firstRelatedDocument->id],
                ],
            ],
            [
                'hits' => [
                    ['id' => $secondRelatedDocument->id],
                ],
            ],
        ],
    ]);

    $service = new MeilisearchRelatedDocumentsSearchService($client);
    $relatedDocuments = $service->search($document, 2);
    $cachedRelatedDocuments = $service->search($document, 2);

    expect($relatedDocuments->pluck('id')->all())->toBe([
        $firstRelatedDocument->id,
        $secondRelatedDocument->id,
    ])
        ->and($cachedRelatedDocuments->pluck('id')->all())->toBe([
            $firstRelatedDocument->id,
            $secondRelatedDocument->id,
        ])
        ->and($client->callCount)->toBe(1)
        ->and($client->queries)->toHaveCount(6)
        ->and($client->queries[0])->toMatchArray([
            'indexUid' => 'documents',
            'q' => 'Ulomki in decimala Seštevanje ulomkov ulomki decimala matematika',
            'filter' => [
                "school_type_id = {$schoolType->id}",
                "subject_id = {$subject->id}",
                "grade_id = {$grade->id}",
                "category_id = {$category->id}",
            ],
            'limit' => 4,
            'attributesToRetrieve' => ['id'],
        ])
        ->and($client->queries[5])->toMatchArray([
            'indexUid' => 'documents',
            'q' => 'Ulomki in decimala Seštevanje ulomkov ulomki decimala matematika',
            'limit' => 4,
            'attributesToRetrieve' => ['id'],
        ]);
});

it('falls back to database related documents when meilisearch is unavailable', function () {
    Cache::flush();

    $schoolType = SchoolType::factory()->create();
    $grade = Grade::factory()->create(['school_type_id' => $schoolType->id]);
    $subject = Subject::factory()->forSchoolType($schoolType)->create();
    $category = Category::factory()->create();

    $document = Document::factory()->create([
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    $relatedDocument = Document::factory()->create([
        'school_type_id' => $schoolType->id,
        'grade_id' => $grade->id,
        'subject_id' => $subject->id,
        'category_id' => $category->id,
    ]);

    $service = new MeilisearchRelatedDocumentsSearchService(new FailingMeilisearchClient);

    $relatedDocuments = $service->search($document, 1);

    expect($relatedDocuments)->toHaveCount(1)
        ->and($relatedDocuments->first()->is($relatedDocument))->toBeTrue();
});

final class FakeMeilisearchClient extends Client
{
    /** @var list<array<string, mixed>> */
    public array $queries = [];

    public int $callCount = 0;

    /**
     * @param  array<string, mixed>  $response
     */
    public function __construct(
        private array $response,
    ) {}

    public function multiSearch(array $queries = [], ?MultiSearchFederation $federation = null)
    {
        $this->callCount++;
        $this->queries = array_map(
            fn (SearchQuery $query): array => $query->toArray(),
            $queries,
        );

        return $this->response;
    }
}

final class FailingMeilisearchClient extends Client
{
    public function __construct() {}

    public function multiSearch(array $queries = [], ?MultiSearchFederation $federation = null)
    {
        throw new RuntimeException('Meilisearch is unavailable.');
    }
}
