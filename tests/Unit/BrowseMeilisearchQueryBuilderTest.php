<?php

use App\Services\Browse\BrowseMeilisearchQueryBuilder;
use App\Services\Browse\BrowseSearchInput;

it('builds only one main query when no facets are selected', function () {
    $builder = new BrowseMeilisearchQueryBuilder;

    $plan = $builder->build(
        new BrowseSearchInput(
            q: 'matematika',
            page: 1,
            hitsPerPage: 25,
            sort: 'newest',
        ),
        'documents'
    );

    expect($plan['queries'])->toHaveCount(1)
        ->and($plan['queries'][0]['facets'])->toBe(['school_type_id', 'grade_id', 'subject_id', 'category_id'])
        ->and($plan['facetSources'])->toBe([
            'school_type_id' => 0,
            'grade_id' => 0,
            'subject_id' => 0,
            'category_id' => 0,
        ]);
});

it('builds disjunctive facet queries only for selected facets', function () {
    $builder = new BrowseMeilisearchQueryBuilder;

    $plan = $builder->build(
        new BrowseSearchInput(
            q: 'matematika',
            page: 1,
            hitsPerPage: 25,
            sort: 'newest',
            schoolTypeId: 11,
            gradeId: 22,
        ),
        'documents'
    );

    expect($plan['queries'])->toHaveCount(3)
        ->and($plan['queries'][0]['filter'])->toBe(['school_type_id = 11', 'grade_id = 22'])
        ->and($plan['queries'][0]['facets'])->toBe(['subject_id', 'category_id'])
        ->and($plan['queries'][1]['facets'])->toBe(['school_type_id'])
        ->and($plan['queries'][1]['filter'])->toBe(['grade_id = 22'])
        ->and($plan['queries'][1]['limit'])->toBe(0)
        ->and($plan['queries'][2]['facets'])->toBe(['grade_id'])
        ->and($plan['queries'][2]['filter'])->toBe(['school_type_id = 11'])
        ->and($plan['queries'][2]['limit'])->toBe(0)
        ->and($plan['facetSources'])->toBe([
            'subject_id' => 0,
            'category_id' => 0,
            'school_type_id' => 1,
            'grade_id' => 2,
        ]);
});
