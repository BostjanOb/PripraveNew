<?php

use App\Models\Comment;
use App\Models\Document;
use Illuminate\Support\Facades\Gate;
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

        if (! $comment) {
            return;
        }

        Gate::authorize('delete', $comment);
        $comment->delete();
        $this->document->load(['comments' => fn ($q) => $q->with('user')->latest()]);
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
                        @can('delete', $comment)
                            <button
                                wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="Ali ste prepričani, da želite izbrisati ta komentar?"
                                class="ml-auto text-xs text-muted-foreground transition-colors hover:text-destructive"
                                title="Izbriši komentar"
                            >
                                <x-icon-regular.trash-can  class="size-3.5"/>
                            </button>
                        @endcan
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
                <x-icon-regular.comment-lines class="mx-auto size-8 text-violet-300 dark:text-violet-600"/>
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
                    <x-icon-regular.paper-plane-top class="size-3.5"/>
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
