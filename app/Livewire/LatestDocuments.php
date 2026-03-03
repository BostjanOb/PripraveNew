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

    /** @var Collection<int, SchoolType> */
    public Collection $schoolTypes;

    /** @var array<string, array<string, mixed>> */
    public array $schoolTypeConfig = [];

    public function mount(): void
    {
        $this->schoolTypes = SchoolType::orderBy('sort_order')->get();
        $this->schoolTypeConfig = SchoolTypeUiConfig::all();
    }

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

        return view('livewire.latest-documents', [
            'documents' => $documents,
            'schoolTypes' => $this->schoolTypes,
            'schoolTypeConfig' => $this->schoolTypeConfig,
        ]);
    }
}
