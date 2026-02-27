<?php

namespace App\Services\Browse;

interface BrowseSearchService
{
    public function search(BrowseSearchInput $input): BrowseSearchResult;
}
