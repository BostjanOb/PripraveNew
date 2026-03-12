<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

use function Illuminate\Support\minutes;

class IndexController extends Controller
{
    public function __invoke(): View
    {
        $documentCount = Cache::remember('index.document_count', minutes(30), fn (): int => Document::count());
        $userCount = Cache::remember('index.user_count', minutes(30), fn (): int => User::count());
        $downloadCount = Cache::remember('index.download_count', minutes(30), fn (): int => (int) Document::sum('downloads_count'));
        $averageRating = Cache::remember('index.average_rating', minutes(30), fn (): float => round((float) Document::avg('rating_avg'), 2));

        $schoolTypes = Cache::remember('index.school_types', minutes(30), fn () => SchoolType::withCount('documents')
            ->orderBy('sort_order')
            ->get());

        return view('pages.index')
            ->with('documentCount', $documentCount)
            ->with('userCount', $userCount)
            ->with('downloadCount', $downloadCount)
            ->with('averageRating', $averageRating)
            ->with('schoolTypes', $schoolTypes);
    }
}
