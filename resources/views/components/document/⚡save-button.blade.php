<?php

use App\Models\Document;
use Livewire\Component;

new class extends Component
{
    public Document $document;

    public bool $isSaved = false;

    public string $context = 'mobile';

    public function mount(Document $document, bool $isSaved = false, string $context = 'mobile'): void
    {
        $this->document = $document;
        $this->isSaved = $isSaved;
        $this->context = $context;
    }

    public function toggle(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if ($this->isSaved) {
            $user->savedDocuments()->detach($this->document->id);
            $this->isSaved = false;
        } else {
            $user->savedDocuments()->attach($this->document->id);
            $this->isSaved = true;
        }

        $this->dispatch('save-toggled', documentId: $this->document->id, saved: $this->isSaved);
    }
};
?>
<div>
    <button
        wire:click="toggle"
        class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-medium transition-colors {{ $isSaved ? 'border-rose-300 bg-rose-50 text-rose-600 dark:border-rose-700 dark:bg-rose-950/50 dark:text-rose-400' : 'border-border bg-background text-foreground hover:bg-secondary' }}"
    >
        @if($isSaved)
            <x-icon-solid.heart  class="size-4 text-rose-500" />
            Shranjeno
        @else
            <x-icon-regular.heart  class="size-4" />
            Shrani
        @endif
    </button>
</div>
