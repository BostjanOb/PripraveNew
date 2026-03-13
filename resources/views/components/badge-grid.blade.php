@use('App\Enums\Badge')
@use('App\Enums\BadgeCategory')

@props([
    'earnedBadges',       // Collection<int, Badge>
    'compact' => false,   // compact mode: only earned badges, single flex row, expandable
])

@php
    $earnedSet = $earnedBadges->all();
    $earnedCount = count($earnedSet);
    $totalCount = count(Badge::cases());
@endphp

@if($compact)
    {{-- Compact mode: only earned badges, expandable with Alpine --}}
    @php
        $visibleCount = 6;
        $hasMore = $earnedCount > $visibleCount;
    @endphp
    <div x-data="{ expanded: false }">
        <div class="flex flex-wrap gap-3">
            @foreach($earnedSet as $index => $badge)
                <div x-show="{{ $index < $visibleCount ? 'true' : 'expanded' }}">
                    <x-badge-icon :badge="$badge" :earned="true" size="sm" :showLabel="true" />
                </div>
            @endforeach

            @if($hasMore)
                <button
                    x-show="!expanded"
                    x-on:click="expanded = true"
                    class="flex size-10 items-center justify-center rounded-2xl border-2 border-dashed border-muted-foreground/20 text-xs font-bold text-muted-foreground transition-colors hover:border-muted-foreground/40"
                >
                    +{{ $earnedCount - $visibleCount }}
                </button>
            @endif
        </div>

        @if($hasMore)
            <button
                x-show="expanded"
                x-on:click="expanded = false"
                class="mt-2 flex items-center gap-1 text-xs font-medium text-muted-foreground hover:text-foreground"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                </svg>
                Pokaži manj
            </button>
        @endif
    </div>
@else
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
    @foreach(BadgeCategory::cases() as $category)
        @php
            $badges = Badge::forCategory($category);
            $catColor = $category->color();
        @endphp
        <div>
            <div class="mb-3 flex items-center gap-2">
                <div class="flex size-6 items-center justify-center rounded-lg {{ $catColor['bg'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 {{ $catColor['text'] }}">
                        {!! $category->iconPath() !!}
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-foreground">{{ $category->label() }}</h3>
            </div>
            <div class="flex flex-wrap gap-4">
                @foreach($badges as $badge)
                    <x-badge-icon
                        :badge="$badge"
                        :earned="$earnedBadges->contains($badge)"
                        size="md"
                        :showLabel="true"
                    />
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endif
