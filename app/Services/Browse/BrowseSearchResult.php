<?php

namespace App\Services\Browse;

use Illuminate\Support\Collection;

final readonly class BrowseSearchResult
{
    /**
     * @param  Collection<int, \App\Models\Document>  $documents
     * @param  array<string, array<int, int>>  $facetCounts
     */
    public function __construct(
        public Collection $documents,
        public int $totalHits,
        public int $totalPages,
        public int $currentPage,
        public array $facetCounts,
    ) {}
}
