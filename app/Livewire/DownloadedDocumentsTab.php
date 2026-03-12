<?php

namespace App\Livewire;

use App\Models\Document;
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

        $downloadedIds = $user->downloadRecords()->pluck('document_id');

        $documents = Document::whereIn('id', $downloadedIds)
            ->with(['schoolType', 'category', 'grade', 'subject', 'user'])
            ->latest()
            ->paginate(10);

        return view('livewire.downloaded-documents-tab', [
            'documents' => $documents,
        ]);
    }
}
