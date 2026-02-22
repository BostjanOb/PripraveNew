@props([
    'document',        // Document model with schoolType, category, grade, subject loaded
    'showActions' => false,  // true for uploaded tab (shows edit/delete)
    'accentColor' => 'teal', // 'teal' for downloaded/saved, 'emerald' for uploaded
])

@php
    $schoolTypeSlug = $document->schoolType?->slug ?? '';

    $schoolTypeStyles = [
        'pv' => ['badge' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 dark:bg-fuchsia-950/50 dark:text-fuchsia-300 dark:border-fuchsia-800', 'dot' => 'bg-fuchsia-500'],
        'os' => ['badge' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800', 'dot' => 'bg-teal-500'],
        'ss' => ['badge' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800', 'dot' => 'bg-orange-500'],
    ];
    $st = $schoolTypeStyles[$schoolTypeSlug] ?? $schoolTypeStyles['os'];

    $schoolTypeLabel = [
        'pv' => 'PV',
        'os' => 'OS',
        'ss' => 'SS',
    ];

    $categorySlug = $document->category?->slug ?? 'priprava';
    $categoryTypeStyles = [
        'priprava'              => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800', 'abbr' => 'P'],
        'delovni-list'          => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800', 'abbr' => 'DL'],
        'test'                  => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'T'],
        'preverjanje-znanja'    => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'PZ'],
        'ucni-list'             => ['badge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800', 'abbr' => 'UL'],
        'ostalo'                => ['badge' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-950/50 dark:text-gray-300 dark:border-gray-800', 'abbr' => 'O'],
    ];
    $ts = $categoryTypeStyles[$categorySlug] ?? $categoryTypeStyles['priprava'];

    $hoverClass = $showActions
        ? 'hover:border-emerald-200 hover:shadow-md hover:shadow-emerald-50/50'
        : 'hover:border-teal-200 hover:shadow-md hover:shadow-teal-50/50';
    $titleHoverClass = $showActions ? 'hover:text-emerald-700' : 'group-hover:text-teal-700';
@endphp

<div class="group flex flex-col gap-3 rounded-2xl border border-border bg-background p-4 transition-all {{ $hoverClass }} md:flex-row md:items-center md:justify-between md:gap-4"
     @if(!$showActions) wire:key="doc-{{ $document->id }}" @endif>

    <div class="flex items-start gap-3 md:flex-1">
        {{-- Category type badge / icon --}}
        <div class="mt-0.5 flex size-11 shrink-0 items-center justify-center rounded-xl text-xs font-bold {{ $ts['badge'] }}">
            {{ $ts['abbr'] }}
        </div>

        <div class="min-w-0 flex-1">
            @if($showActions)
                <a href="{{ url('/dokument/' . $document->slug) }}"
                   class="font-semibold text-foreground transition-colors {{ $titleHoverClass }}">
                    {{ $document->title }}
                </a>
            @else
                <a href="{{ url('/dokument/' . $document->slug) }}"
                   class="font-semibold text-foreground transition-colors {{ $titleHoverClass }}">
                    {{ $document->title }}
                </a>
            @endif

            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                @if(!$showActions)
                    <span class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        {{ $document->user?->display_name }}
                    </span>
                @endif
                <span class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $document->created_at->format('d.m.Y') }}
                </span>
                @if($showActions)
                    <span class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        {{ $document->downloads_count }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="flex gap-2 @if($showActions) flex-col md:items-end @else items-center md:justify-end @endif">
        <div class="flex items-center gap-2 @if($showActions) flex-wrap  md:justify-end @endif">
            {{-- School type badge --}}
            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $st['badge'] }}">
                <span class="inline-block size-2 rounded-full {{ $st['dot'] }}"></span>
                {{ $schoolTypeLabel[$schoolTypeSlug] ?? $schoolTypeSlug }}
            </span>

            {{-- Grade --}}
            @if($document->grade)
                <span class="inline-flex items-center rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-300">
                    {{ $document->grade->name }}
                </span>
            @endif

            {{-- Subject --}}
            @if($document->subject)
                <span class="inline-flex items-center rounded-full border border-pink-200 bg-pink-50 px-2.5 py-0.5 text-xs font-medium text-pink-700 dark:border-pink-800 dark:bg-pink-950/50 dark:text-pink-300">
                    {{ $document->subject->name }}
                </span>
            @endif

            @if(!$showActions)
                <div class="hidden items-center gap-3 pl-3 text-xs text-muted-foreground md:flex">
                    <span class="flex items-center gap-1">
                        <x-icon-regular.download class="size-3" />
                        {{ $document->downloads_count }}
                    </span>
                </div>
            @endif
        </div>

        @if($showActions)
            {{-- Edit / Delete buttons --}}
            <div class="grid grid-cols-2 gap-2 md:w-auto">
                <a href="{{ url('/dodajanje?uredi=' . $document->id) }}"
                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-sm font-medium text-emerald-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 dark:border-emerald-800 dark:bg-background dark:text-emerald-300 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Uredi
                </a>
                <button wire:click="deleteDocument({{ $document->id }})"
                        wire:confirm="Ali želiš izbrisati to pripravo?"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-sm font-medium text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 dark:border-rose-800 dark:bg-background dark:text-rose-300 dark:hover:bg-rose-950/50 dark:hover:text-rose-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Izbriši
                </button>
            </div>
        @endif
    </div>
</div>
