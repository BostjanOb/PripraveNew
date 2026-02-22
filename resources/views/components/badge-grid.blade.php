@props([
    'allBadges',      // array from BadgeRegistry::all()
    'earnedBadgeIds', // string[]
    'categories',     // string[] from BadgeRegistry::categories()
    'categoryLabels', // array<string,string> from BadgeRegistry::categoryLabels()
])

@php
    $earnedSet = array_flip($earnedBadgeIds);
    $earnedCount = count($earnedBadgeIds);
    $totalCount = count($allBadges);

    $categoryIcons = [
        'contribution' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />',
        'downloads'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />',
        'loyalty'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />',
        'special'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />',
    ];
    $categoryColors = [
        'contribution' => ['text' => 'text-emerald-600 dark:text-emerald-300', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/40'],
        'downloads'    => ['text' => 'text-sky-600 dark:text-sky-300',     'bg' => 'bg-sky-50 dark:bg-sky-950/40'],
        'loyalty'      => ['text' => 'text-pink-600 dark:text-pink-300',   'bg' => 'bg-pink-50 dark:bg-pink-950/40'],
        'special'      => ['text' => 'text-fuchsia-600 dark:text-fuchsia-300', 'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/40'],
    ];
@endphp

<div class="space-y-6">
    {{-- Summary --}}
    <div class="flex items-center gap-2">
        <div class="flex size-8 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-amber-600 dark:text-amber-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
            </svg>
        </div>
        <div>
            <span class="text-sm font-semibold text-foreground">{{ $earnedCount }} / {{ $totalCount }} značk</span>
            <span class="ml-2 text-xs text-muted-foreground">osvojenih</span>
        </div>
    </div>

    {{-- Per-category grids --}}
    @foreach($categories as $cat)
        @php
            $badges = array_values(array_filter($allBadges, fn($b) => $b['category'] === $cat));
            $color = $categoryColors[$cat] ?? $categoryColors['contribution'];
            $iconPath = $categoryIcons[$cat] ?? $categoryIcons['contribution'];
        @endphp
        <div>
            <div class="mb-3 flex items-center gap-2">
                <div class="flex size-6 items-center justify-center rounded-lg {{ $color['bg'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 {{ $color['text'] }}">
                        {!! $iconPath !!}
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-foreground">{{ $categoryLabels[$cat] }}</h3>
            </div>
            <div class="flex flex-wrap gap-4">
                @foreach($badges as $badge)
                    <x-badge-icon
                        :badge="$badge"
                        :earned="isset($earnedSet[$badge['id']])"
                        size="md"
                        :showLabel="true"
                    />
                @endforeach
            </div>
        </div>
    @endforeach
</div>
