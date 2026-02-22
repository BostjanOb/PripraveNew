<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\SchoolType;
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
        $query = Document::query()
            ->with(['user', 'schoolType', 'grade', 'subject', 'category']);

        if ($this->activeType !== 'all') {
            $query->whereHas('schoolType', fn ($q) => $q->where('slug', $this->activeType));
        }

        $documents = $query->latest()->limit(10)->get();

        /** @var Collection<int, SchoolType> $schoolTypes */
        $schoolTypes = SchoolType::query()->orderBy('sort_order')->get();

        return view('livewire.latest-documents', [
            'documents' => $documents,
            'schoolTypes' => $schoolTypes,
        ]);
    }
}
