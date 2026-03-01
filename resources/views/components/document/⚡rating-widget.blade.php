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

        Rating::updateOrCreate(
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
        <x-icon-regular.sparkles class="size-4 text-amber-500" />
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
                    <x-icon-duotone-regular.star class="size-8 transition-colors"
                                         x-bind:class="({{ $star }} <= (hoveredStar || {{ $userRating ?? 0 }})) ? 'fill-amber-400 text-amber-400 stroke-amber-400' : 'fill-none text-amber-200 stroke-amber-200'"/>
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
                    <x-icon-duotone-regular.star  class="size-8 text-amber-200" />
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
            Povprečna ocena: {{ $document->rating_avg }} ({{ $document->rating_count }} {{ $document->rating_count === 1 ? 'glas' : 'glasov' }})
        </p>
    @endif
</div>
