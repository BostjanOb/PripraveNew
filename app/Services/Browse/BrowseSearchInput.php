<?php

namespace App\Services\Browse;

final readonly class BrowseSearchInput
{
    /**
     * @param  list<int>  $categoryIds
     */
    public function __construct(
        public string $q,
        public int $page,
        public int $hitsPerPage,
        public string $sort,
        public ?int $schoolTypeId = null,
        public ?int $gradeId = null,
        public ?int $subjectId = null,
        public array $categoryIds = [],
    ) {}
}
