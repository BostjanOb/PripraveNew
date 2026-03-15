<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class DownloadedDocumentsTab extends Component
{
    use WithPagination;

    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $documents = $user->downloadedDocuments()
            ->with(['schoolType', 'category', 'grade', 'subject', 'user'])
            ->latest('document_user.created_at')
            ->paginate(15);

        return view('livewire.downloaded-documents-tab', [
            'documents' => $documents,
        ]);
    }
}
