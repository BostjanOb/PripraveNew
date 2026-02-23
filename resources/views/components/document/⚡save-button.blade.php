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
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-rose-500">
                <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
            </svg>
            Shranjeno
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
            Shrani
        @endif
    </button>
</div>
