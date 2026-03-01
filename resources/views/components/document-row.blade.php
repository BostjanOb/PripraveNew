@props([
    'document',        // Document model with schoolType, category, grade, subject loaded
    'showActions' => false,  // true for uploaded tab (shows edit/delete)
    'accentColor' => 'teal', // 'teal' for downloaded/saved, 'emerald' for uploaded
])

@php
    $schoolTypeConfig = \App\Support\SchoolTypeUiConfig::all();
    $schoolTypeSlug = $document->schoolType?->slug ?? '';
    $st = $schoolTypeConfig[$schoolTypeSlug] ?? $schoolTypeConfig['os'];

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

<div class="group relative flex flex-col gap-3 rounded-2xl border border-border bg-card p-4 transition-all {{ $hoverClass }} md:flex-row md:items-center md:justify-between md:gap-4"
     @if(!$showActions) wire:key="doc-{{ $document->id }}" @endif>

    @if(!$showActions)
        <a href="{{ route('document.show', $document) }}" class="absolute inset-0 rounded-2xl" aria-label="{{ $document->title }}"></a>
    @endif

    <div class="flex items-start gap-3 md:flex-1">
        {{-- Category type badge / icon --}}
        <div class="mt-0.5 flex size-11 shrink-0 items-center justify-center rounded-xl text-xs font-bold {{ $ts['badge'] }}">
            {{ $ts['abbr'] }}
        </div>

        <div class="min-w-0 flex-1">
            @if($showActions)
                <a href="{{ route('document.show', $document) }}"
                   class="font-semibold text-foreground transition-colors {{ $titleHoverClass }}">
                    {{ $document->title }}
                </a>
            @else
                <span class="font-semibold text-foreground transition-colors {{ $titleHoverClass }}">
                    {{ $document->title }}
                </span>
            @endif

            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                @if(!$showActions)
                    <span class="flex items-center gap-1">
                        <x-icon-regular.user class="size-3" />
                        {{ $document->user?->display_name }}
                    </span>
                @endif
                <span class="flex items-center gap-1">
                    <x-icon-regular.clock class="size-3" />
                    {{ $document->created_at->format('d.m.Y') }}
                </span>
                @if($showActions)
                    <span class="flex items-center gap-1">
                        <x-icon-regular.download class="size-3" />
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
                {{ $st['shortLabel'] }}
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
                    <x-icon-regular.pen-to-square class="size-4" />
                    Uredi
                </a>
                <button wire:click="deleteDocument({{ $document->id }})"
                        wire:confirm="Ali želiš izbrisati to pripravo?"
                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-sm font-medium text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 dark:border-rose-800 dark:bg-background dark:text-rose-300 dark:hover:bg-rose-950/50 dark:hover:text-rose-200">
                    <x-icon-regular.trash-can class="size-4" />
                    Izbriši
                </button>
            </div>
        @endif
    </div>
</div>
