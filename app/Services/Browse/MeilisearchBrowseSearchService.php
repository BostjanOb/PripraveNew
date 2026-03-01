<?php

namespace App\Services\Browse;

use App\Models\Document;
use Illuminate\Support\Collection;
use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;

final class MeilisearchBrowseSearchService implements BrowseSearchService
{
    public function __construct(
        private Client $meilisearchClient,
        private BrowseMeilisearchQueryBuilder $queryBuilder,
    ) {}

    public function search(BrowseSearchInput $input): BrowseSearchResult
    {
        $indexUid = (string) config('scout.prefix').(new Document)->searchableAs();
        $queryPlan = $this->queryBuilder->build($input, $indexUid);

        $searchQueries = collect($queryPlan['queries'])
            ->map(fn (array $query): SearchQuery => $this->toSearchQuery($query))
            ->all();

        $response = $this->meilisearchClient->multiSearch($searchQueries);
        $results = $response['results'] ?? [];
        $mainResult = $results[0] ?? [];
        $documentIds = collect($mainResult['hits'] ?? [])->pluck('id')->map(fn (mixed $id): int => (int) $id);

        $documents = $this->hydrateDocuments($documentIds);
        $totalHits = (int) ($mainResult['totalHits'] ?? $mainResult['estimatedTotalHits'] ?? 0);
        $totalPages = (int) ($mainResult['totalPages'] ?? max(1, (int) ceil($totalHits / $input->hitsPerPage)));
        $currentPage = (int) ($mainResult['page'] ?? $input->page);

        return new BrowseSearchResult(
            documents: $documents,
            totalHits: $totalHits,
            totalPages: $totalPages,
            currentPage: $currentPage,
            facetCounts: $this->resolveFacetCounts($results, $queryPlan['facetSources']),
        );
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
            ->with(['user', 'schoolType', 'grade', 'subject', 'category'])
            ->get()
            ->sortBy(fn (Document $document): int => (int) $documentIds->search($document->id))
            ->values();
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     * @param  array<string, int>  $facetSources
     * @return array<string, array<int, int>>
     */
    private function resolveFacetCounts(array $results, array $facetSources): array
    {
        $counts = [];

        foreach ($facetSources as $facetKey => $resultIndex) {
            $distribution = $results[$resultIndex]['facetDistribution'][$facetKey] ?? [];
            $counts[$facetKey] = collect($distribution)
                ->mapWithKeys(fn (mixed $count, mixed $id): array => [(int) $id => (int) $count])
                ->all();
        }

        return $counts;
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

        if (isset($query['sort'])) {
            $searchQuery->setSort($query['sort']);
        }

        if (isset($query['facets'])) {
            $searchQuery->setFacets($query['facets']);
        }

        if (isset($query['hitsPerPage'])) {
            $searchQuery->setHitsPerPage($query['hitsPerPage']);
        }

        if (isset($query['page'])) {
            $searchQuery->setPage($query['page']);
        }

        if (isset($query['limit'])) {
            $searchQuery->setLimit($query['limit']);
        }

        return $searchQuery;
    }
}
