@props([
    'badge',      // App\Enums\Badge
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
    $color = $badge->color();
    $title = $earned
        ? ($badge->label() . ' — ' . $badge->description())
        : ($badge->label() . ' — ' . $badge->requirement());
@endphp

<flux:tooltip :content="$title">
    <div class="flex flex-col items-center gap-1.5">
        <div class="{{ $s['wrapper'] }} relative flex items-center justify-center rounded-2xl border-2 transition-all
            {{ $earned
                ? $color['border'] . ' ' . $color['bg'] . ' shadow-sm'
                : 'border-gray-200 bg-gray-100 dark:border-slate-700 dark:bg-slate-800/80'
            }}">

            <x-dynamic-component :component="$badge->icon()"
                         :class="$s['icon'] . ' ' . ($earned ? $color['text'] : 'text-gray-300 dark:text-slate-500')" />

            @unless($earned)
                <div class="absolute -bottom-1 -right-1 flex size-4 items-center justify-center rounded-full bg-gray-200 dark:bg-slate-700">
                    <x-icon-regular.lock class="size-2.5 text-gray-400 dark:text-slate-300" />
                </div>
            @endunless
        </div>

        @if($showLabel)
            <span class="{{ $s['text'] }} text-center font-medium leading-tight
                {{ $earned ? 'text-foreground' : 'text-muted-foreground/50' }}">
                {{ $badge->label() }}
            </span>
        @endif
    </div>
</flux:tooltip>
