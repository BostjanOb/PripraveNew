<?php

namespace App\Services\Documents;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Throwable;

final readonly class MeilisearchRelatedDocumentsSearchService implements RelatedDocumentsSearchService
{
    public function __construct(
        private Client $meilisearchClient,
    ) {}

    /**
     * @return Collection<int, Document>
     */
    public function search(Document $document, int $limit = 3): Collection
    {
        try {
            return Cache::remember(
                $this->cacheKey($document, $limit),
                now()->addDay(),
                function () use ($document, $limit): Collection {
                    $searchQueries = collect($this->buildQueries($document, $limit))
                        ->map(fn (array $query): SearchQuery => $this->toSearchQuery($query))
                        ->all();

                    $response = $this->meilisearchClient->multiSearch($searchQueries);
                    $documentIds = collect($response['results'] ?? [])
                        ->flatMap(fn (array $result): Collection => collect($result['hits'] ?? [])->pluck('id'))
                        ->map(fn (mixed $id): int => (int) $id)
                        ->reject(fn (int $id): bool => $id === $document->id)
                        ->unique()
                        ->take($limit)
                        ->values();

                    return $this->hydrateDocuments($documentIds);
                },
            );
        } catch (Throwable) {
            return $this->fallbackDocuments($document, $limit);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildQueries(Document $document, int $limit): array
    {
        $availableFilters = [
            'school_type_id' => $this->filterExpressionFor('school_type_id', $document->school_type_id),
            'subject_id' => $this->filterExpressionFor('subject_id', $document->subject_id),
            'grade_id' => $this->filterExpressionFor('grade_id', $document->grade_id),
            'category_id' => $this->filterExpressionFor('category_id', $document->category_id),
        ];

        $filterSets = collect([
            ['school_type_id', 'subject_id', 'grade_id', 'category_id'],
            ['school_type_id', 'subject_id', 'category_id'],
            ['school_type_id', 'subject_id'],
            ['school_type_id', 'category_id'],
            ['school_type_id'],
            [],
        ])
            ->map(function (array $keys) use ($availableFilters): array {
                return array_values(
                    array_filter(
                        array_map(fn (string $key): ?string => $availableFilters[$key] ?? null, $keys)
                    )
                );
            })
            ->unique(fn (array $filters): string => implode('|', $filters))
            ->values();

        return $filterSets
            ->map(function (array $filters) use ($document, $limit): array {
                $query = [
                    'indexUid' => $this->indexUid(),
                    'q' => $this->queryText($document),
                    'limit' => $limit + 2,
                    'attributesToRetrieve' => ['id'],
                ];

                if ($filters !== []) {
                    $query['filter'] = $filters;
                }

                return $query;
            })
            ->all();
    }

    private function filterExpressionFor(string $attribute, ?int $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return "{$attribute} = {$value}";
    }

    private function indexUid(): string
    {
        return (string) config('scout.prefix').(new Document)->searchableAs();
    }

    private function queryText(Document $document): string
    {
        return collect([
            $document->title,
            $document->topic,
            $document->keywords,
        ])
            ->filter(fn (?string $value): bool => filled($value))
            ->implode(' ');
    }

    private function cacheKey(Document $document, int $limit): string
    {
        $updatedAt = $document->updated_at?->timestamp ?? 'na';

        return "documents.related-search.{$document->id}.{$limit}.{$updatedAt}";
    }

    /**
     * @param  Collection<int, int>  $documentIds
     * @return Collection<int, Document>
     */
    private function hydrateDocuments(Collection $documentIds): Collection
    {
        if ($documentIds->isEmpty()) {
            return collect();
        }

        return Document::whereIn('id', $documentIds->all())
            ->with(['category', 'schoolType', 'grade', 'subject'])
            ->get()
            ->sortBy(fn (Document $relatedDocument): int => (int) $documentIds->search($relatedDocument->id))
            ->values();
    }

    /**
     * @return Collection<int, Document>
     */
    private function fallbackDocuments(Document $document, int $limit): Collection
    {
        return $document->relatedDocuments($limit)
            ->loadMissing(['category', 'schoolType', 'grade', 'subject', 'user']);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function toSearchQuery(array $query): SearchQuery
    {
        $searchQuery = (new SearchQuery)
            ->setIndexUid($query['indexUid'])
            ->setQuery($query['q']);

        if (isset($query['filter'])) {
            $searchQuery->setFilter($query['filter']);
        }

        if (isset($query['limit'])) {
            $searchQuery->setLimit($query['limit']);
        }

        if (isset($query['attributesToRetrieve'])) {
            $searchQuery->setAttributesToRetrieve($query['attributesToRetrieve']);
        }

        return $searchQuery;
    }
}
