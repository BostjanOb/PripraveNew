<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\View\View;

class IndexController extends Controller
{
    public function __invoke(): View
    {
        $documentCount = Document::count();
        $userCount = User::count();
        $downloadCount = (int) Document::sum('downloads_count');
        $averageRating = round((float) Document::avg('rating_avg'), 2);

        $schoolTypes = SchoolType::query()
            ->withCount('documents')
            ->orderBy('sort_order')
            ->get();

        return view('pages.index')
            ->with('documentCount', $documentCount)
            ->with('userCount', $userCount)
            ->with('downloadCount', $downloadCount)
            ->with('averageRating', $averageRating)
            ->with('schoolTypes', $schoolTypes);
    }
}
