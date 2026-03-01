<?php

namespace App\Livewire;

use App\Models\Document;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PublicUploadedDocuments extends Component
{
    use WithPagination;

    public int $userId;

    public function render(): View
    {
        $documents = Document::where('user_id', $this->userId)
            ->with(['schoolType', 'category', 'grade', 'subject'])
            ->latest()
            ->paginate(5);

        return view('livewire.public-uploaded-documents', [
            'documents' => $documents,
        ]);
    }
}
