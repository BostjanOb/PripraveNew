<?php

use App\Models\Document;
use App\Models\Rating;
use Livewire\Component;

new class extends Component
{
    public Document $document;

    public ?int $userRating = null;

    public function mount(Document $document, ?int $userRating = null): void
    {
        $this->document = $document;
        $this->userRating = $userRating;
    }

    public function rate(int $stars): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if ($stars < 1 || $stars > 5) {
            return;
        }

        Rating::query()->updateOrCreate(
            ['document_id' => $this->document->id, 'user_id' => $user->id],
            ['rating' => $stars],
        );

        $this->userRating = $stars;

        $this->document->recalculateRating();
        $this->document->refresh();
    }
};
?>

<div class="rounded-2xl border border-border bg-card p-5">
    <h3 class="flex items-center gap-2 text-sm font-semibold text-foreground">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-amber-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
        </svg>
        Oceni gradivo
    </h3>

    @auth
        <div class="mt-3 flex items-center gap-1" x-data="{ hoveredStar: 0 }">
            @for($star = 1; $star <= 5; $star++)
                <button
                    wire:click="rate({{ $star }})"
                    x-on:mouseenter="hoveredStar = {{ $star }}"
                    x-on:mouseleave="hoveredStar = 0"
                    class="transition-transform hover:scale-125"
                    aria-label="Oceni {{ $star }} od 5"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
                         class="size-8 transition-colors"
                         x-bind:class="({{ $star }} <= (hoveredStar || {{ $userRating ?? 0 }})) ? 'fill-amber-400 text-amber-400 stroke-amber-400' : 'fill-none text-amber-200 stroke-amber-200'"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                </button>
            @endfor
        </div>
        @if($userRating)
            <p class="mt-2 text-xs font-medium text-amber-600 dark:text-amber-400">
                Tvoja ocena: {{ $userRating }}/5
            </p>
        @endif
    @else
        <div class="mt-3">
            <div class="flex items-center gap-1 opacity-40">
                @for($star = 1; $star <= 5; $star++)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-amber-200">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                @endfor
            </div>
            <p class="mt-2 text-xs text-muted-foreground">
                <a href="{{ route('login') }}" class="font-medium text-primary underline-offset-2 hover:underline">Prijavite se</a>
                za ocenjevanje.
            </p>
        </div>
    @endauth

    @if($document->rating_count > 0)
        <p class="mt-1 text-xs text-muted-foreground">
            Povprecna ocena: {{ $document->rating_avg }} ({{ $document->rating_count }} {{ $document->rating_count === 1 ? 'glas' : 'glasov' }})
        </p>
    @endif
</div>
