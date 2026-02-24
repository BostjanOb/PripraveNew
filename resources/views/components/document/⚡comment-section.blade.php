<?php

use App\Models\Comment;
use App\Models\Document;
use Livewire\Component;

new class extends Component
{
    public Document $document;

    public string $text = '';

    public function mount(Document $document): void
    {
        $this->document = $document;
    }

    public function addComment(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->validate([
            'text' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        Comment::create([
            'document_id' => $this->document->id,
            'user_id' => $user->id,
            'text' => $this->text,
        ]);

        $this->text = '';

        $this->document->load(['comments' => fn ($q) => $q->with('user')->latest()]);
    }

    public function deleteComment(int $commentId): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $comment = Comment::find($commentId);

        if ($comment && $comment->user_id === $user->id) {
            $comment->delete();
            $this->document->load(['comments' => fn ($q) => $q->with('user')->latest()]);
        }
    }
};
?>

<div class="rounded-2xl border border-border bg-card p-6 md:p-8">
    <div class="flex items-center gap-2">
        <div class="flex size-8 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/50">
            <x-icon-regular.comment-lines class="size-4 text-violet-600 dark:text-violet-400"/>
        </div>
        <h2 class="font-serif text-lg font-bold text-foreground">Komentarji</h2>
    </div>

    {{-- Comment list --}}
    @if($document->comments->isNotEmpty())
        <div class="mt-5 space-y-3">
            @foreach($document->comments as $comment)
                <div class="rounded-xl border border-border bg-background p-4" wire:key="comment-{{ $comment->id }}">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold text-foreground">{{ $comment->user?->display_name }}</span>
                        <span class="text-xs text-muted-foreground">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                        @if(auth()->id() === $comment->user_id)
                            <button
                                wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Ali ste prepričani, da želite izbrisati ta komentar?"
                                class="ml-auto text-xs text-muted-foreground transition-colors hover:text-destructive"
                                title="Izbriši komentar"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-muted-foreground">{{ $comment->text }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add comment form or login prompt --}}
    @auth
        @if($document->comments->isEmpty())
            <div class="mt-5 rounded-xl border-2 border-dashed border-violet-200 bg-violet-50/50 py-10 text-center dark:border-violet-800 dark:bg-violet-950/30">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto size-8 text-violet-300 dark:text-violet-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                </svg>
                <p class="mt-2 text-sm text-violet-500 dark:text-violet-400">
                    Priprava še nima komentarja. Bodi prvi!
                </p>
            </div>
        @endif

        <form wire:submit="addComment" class="mt-5">
            <textarea
                wire:model="text"
                placeholder="Napiši komentar..."
                rows="3"
                class="w-full resize-none rounded-xl border border-border bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200 dark:focus:ring-violet-800"
            ></textarea>
            @error('text')
                <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
            @enderror
            <div class="mt-3 flex justify-end">
                <button
                    type="submit"
                    @disabled(! $text)
                    class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                    Komentiraj
                </button>
            </div>
        </form>
    @else
        <div class="mt-5 rounded-xl border-2 border-dashed border-violet-200 bg-violet-50/50 py-8 text-center dark:border-violet-800 dark:bg-violet-950/30">
            <x-icon-regular.lock  class="mx-auto size-8 text-violet-300 dark:text-violet-600"/>

            <p class="mt-2 text-sm font-medium text-violet-600 dark:text-violet-400">
                Prijavite se, da lahko komentirate.
            </p>
            @if($document->comments->isEmpty())
                <p class="mt-1 text-xs text-violet-400 dark:text-violet-500">Še ni komentarjev.</p>
            @endif

            <flux:button href="{{ route('login') }}" icon="icon-regular.arrow-right-from-bracket"
                         size="sm"
                         class="mt-3 bg-violet-600! text-white! transition-colors hover:bg-violet-700!">
                Prijava
            </flux:button>
        </div>
    @endauth
</div>
