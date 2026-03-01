<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class UploadedDocumentsTab extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteDocument(int $id): void
    {
        $document = Document::findOrFail($id);

        if ($document->user_id !== auth()->id()) {
            abort(403);
        }

        $document->delete();
    }

    public function render(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $documents = $user
            ->documents()
            ->with(['schoolType', 'category', 'grade', 'subject'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.uploaded-documents-tab', [
            'documents' => $documents,
        ]);
    }
}
