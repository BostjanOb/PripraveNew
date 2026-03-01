<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\SchoolType;
use App\Support\SchoolTypeUiConfig;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class LatestDocuments extends Component
{
    public string $activeType = 'all';

    public function setActiveType(string $type): void
    {
        $this->activeType = $type;
    }

    public function render(): View
    {
        $documents = Document::with(['user', 'schoolType', 'grade', 'subject', 'category'])
            ->when(
                $this->activeType !== 'all',
                fn ($q) => $q->whereHas('schoolType', fn ($q) => $q->where('slug', $this->activeType))
            )->latest()->limit(10)->get();

        /** @var Collection<int, SchoolType> $schoolTypes */
        $schoolTypes = SchoolType::orderBy('sort_order')->get();

        return view('livewire.latest-documents', [
            'documents' => $documents,
            'schoolTypes' => $schoolTypes,
            'schoolTypeConfig' => SchoolTypeUiConfig::all(),
        ]);
    }
}
