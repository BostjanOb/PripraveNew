@props([
    'badge',      // array from BadgeRegistry
    'earned' => true,
    'size' => 'md',   // sm | md | lg
    'showLabel' => false,
])

@php
    $sizes = [
        'sm' => ['wrapper' => 'size-10', 'icon' => 'size-5', 'text' => 'text-[10px]'],
        'md' => ['wrapper' => 'size-14', 'icon' => 'size-7', 'text' => 'text-xs'],
        'lg' => ['wrapper' => 'size-20', 'icon' => 'size-10', 'text' => 'text-sm'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $title = $earned
        ? ($badge['name'] . ' — ' . $badge['description'])
        : ($badge['name'] . ' — ' . $badge['requirement']);
@endphp

<div class="flex flex-col items-center gap-1.5" title="{{ $title }}">
    <div class="{{ $s['wrapper'] }} relative flex items-center justify-center rounded-2xl border-2 transition-all
        {{ $earned
            ? $badge['color']['border'] . ' ' . $badge['color']['bg'] . ' shadow-sm'
            : 'border-gray-200 bg-gray-100 dark:border-slate-700 dark:bg-slate-800/80'
        }}">

        <x-badge-svg :badgeId="$badge['id']"
                     :class="$s['icon'] . ' ' . ($earned ? $badge['color']['text'] : 'text-gray-300 dark:text-slate-500')" />

        @unless($earned)
            <div class="absolute -bottom-1 -right-1 flex size-4 items-center justify-center rounded-full bg-gray-200 dark:bg-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-2.5 text-gray-400 dark:text-slate-300">
                    <path fill-rule="evenodd" d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7V4.5A3.5 3.5 0 0 0 8 1Zm2 6V4.5a2 2 0 1 0-4 0V7h4Z" clip-rule="evenodd" />
                </svg>
            </div>
        @endunless
    </div>

    @if($showLabel)
        <span class="{{ $s['text'] }} text-center font-medium leading-tight
            {{ $earned ? 'text-foreground' : 'text-muted-foreground/50' }}">
            {{ $badge['name'] }}
        </span>
    @endif
</div>
