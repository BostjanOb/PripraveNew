<?php

namespace App\Services\Browse;

final class BrowseMeilisearchQueryBuilder
{
    /** @var list<string> */
    private const FACET_KEYS = ['school_type_id', 'grade_id', 'subject_id', 'category_id'];

    /**
     * @return array{
     *     queries: list<array<string, mixed>>,
     *     facetSources: array<string, int>
     * }
     */
    public function build(BrowseSearchInput $input, string $indexUid): array
    {
        $activeFilters = $this->buildActiveFilters($input);
        $selectedFacetKeys = array_values(array_keys($activeFilters));
        $nonSelectedFacetKeys = array_values(array_diff(self::FACET_KEYS, $selectedFacetKeys));

        $queries = [];
        $facetSources = [];

        $mainQuery = [
            'indexUid' => $indexUid,
            'q' => $input->q,
            'sort' => $this->mapSort($input->sort),
            'hitsPerPage' => $input->hitsPerPage,
            'page' => $input->page,
        ];

        if ($activeFilters !== []) {
            $mainQuery['filter'] = array_values($activeFilters);
        }

        if ($nonSelectedFacetKeys !== []) {
            $mainQuery['facets'] = $nonSelectedFacetKeys;

            foreach ($nonSelectedFacetKeys as $facetKey) {
                $facetSources[$facetKey] = 0;
            }
        }

        $queries[] = $mainQuery;

        foreach ($selectedFacetKeys as $facetKey) {
            $filtersWithoutFacet = array_values(array_diff_key($activeFilters, [$facetKey => true]));

            $query = [
                'indexUid' => $indexUid,
                'q' => $input->q,
                'facets' => [$facetKey],
                'limit' => 0,
            ];

            if ($filtersWithoutFacet !== []) {
                $query['filter'] = $filtersWithoutFacet;
            }

            $queries[] = $query;
            $facetSources[$facetKey] = count($queries) - 1;
        }

        return [
            'queries' => $queries,
            'facetSources' => $facetSources,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function buildActiveFilters(BrowseSearchInput $input): array
    {
        $filters = [];

        if ($input->schoolTypeId !== null) {
            $filters['school_type_id'] = "school_type_id = {$input->schoolTypeId}";
        }

        if ($input->gradeId !== null) {
            $filters['grade_id'] = "grade_id = {$input->gradeId}";
        }

        if ($input->subjectId !== null) {
            $filters['subject_id'] = "subject_id = {$input->subjectId}";
        }

        $categoryIds = array_values(array_unique(array_map('intval', $input->categoryIds)));

        if ($categoryIds !== []) {
            $filters['category_id'] = 'category_id IN ['.implode(', ', $categoryIds).']';
        }

        return $filters;
    }

    /**
     * @return list<string>
     */
    private function mapSort(string $sort): array
    {
        return match ($sort) {
            'oldest' => ['created_at_ts:asc'],
            'most-downloaded' => ['downloads_count:desc'],
            'most-viewed' => ['views_count:desc'],
            default => ['created_at_ts:desc'],
        };
    }
}
