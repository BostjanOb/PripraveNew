<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SavedDocumentsTab extends Component
{
    use WithPagination;

    public function render(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $documents = $user
            ->savedDocuments()
            ->with(['schoolType', 'category', 'grade', 'subject', 'user'])
            ->latest('saved_documents.created_at')
            ->paginate(10);

        return view('livewire.saved-documents-tab', [
            'documents' => $documents,
        ]);
    }
}
