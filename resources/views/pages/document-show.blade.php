<x-layouts.app :title="$document->title . ' — Priprave.net'">

@php
    // ── Color palette mappings ──
    $categorySlug = $document->category?->slug ?? 'priprava';
    $typePalette = [
        'priprava'           => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/50', 'text' => 'text-emerald-700 dark:text-emerald-300', 'border' => 'border-emerald-200 dark:border-emerald-800', 'icon' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400'],
        'delovni-list'       => ['bg' => 'bg-amber-50 dark:bg-amber-950/50', 'text' => 'text-amber-700 dark:text-amber-300', 'border' => 'border-amber-200 dark:border-amber-800', 'icon' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400'],
        'test'               => ['bg' => 'bg-rose-50 dark:bg-rose-950/50', 'text' => 'text-rose-700 dark:text-rose-300', 'border' => 'border-rose-200 dark:border-rose-800', 'icon' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400'],
        'preverjanje-znanja' => ['bg' => 'bg-rose-50 dark:bg-rose-950/50', 'text' => 'text-rose-700 dark:text-rose-300', 'border' => 'border-rose-200 dark:border-rose-800', 'icon' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400'],
        'ucni-list'          => ['bg' => 'bg-sky-50 dark:bg-sky-950/50', 'text' => 'text-sky-700 dark:text-sky-300', 'border' => 'border-sky-200 dark:border-sky-800', 'icon' => 'bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400'],
        'ostalo'             => ['bg' => 'bg-gray-50 dark:bg-gray-950/50', 'text' => 'text-gray-700 dark:text-gray-300', 'border' => 'border-gray-200 dark:border-gray-800', 'icon' => 'bg-gray-100 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400'],
    ];
    $tp = $typePalette[$categorySlug] ?? $typePalette['priprava'];

    $schoolTypeSlug = $document->schoolType?->slug ?? 'os';
    $levelPalette = [
        'pv' => ['bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/50', 'text' => 'text-fuchsia-700 dark:text-fuchsia-300', 'border' => 'border-fuchsia-200 dark:border-fuchsia-800', 'dot' => 'bg-fuchsia-500', 'icon' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-600 dark:text-fuchsia-400'],
        'os' => ['bg' => 'bg-teal-50 dark:bg-teal-950/50', 'text' => 'text-teal-700 dark:text-teal-300', 'border' => 'border-teal-200 dark:border-teal-800', 'dot' => 'bg-teal-500', 'icon' => 'bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400'],
        'ss' => ['bg' => 'bg-orange-50 dark:bg-orange-950/50', 'text' => 'text-orange-700 dark:text-orange-300', 'border' => 'border-orange-200 dark:border-orange-800', 'dot' => 'bg-orange-500', 'icon' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400'],
    ];
    $lp = $levelPalette[$schoolTypeSlug] ?? $levelPalette['os'];

    $gradePalette = ['bg' => 'bg-indigo-50 dark:bg-indigo-950/50', 'text' => 'text-indigo-700 dark:text-indigo-300', 'border' => 'border-indigo-200 dark:border-indigo-800', 'icon' => 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400'];
    $subjectPalette = ['bg' => 'bg-pink-50 dark:bg-pink-950/50', 'text' => 'text-pink-700 dark:text-pink-300', 'border' => 'border-pink-200 dark:border-pink-800', 'icon' => 'bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400'];

    // File color helpers
    $fileColor = fn (string $ext) => match(true) {
        in_array($ext, ['jpg', 'jpeg', 'png']) => 'bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400',
        in_array($ext, ['doc', 'docx']) => 'bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400',
        $ext === 'pdf' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400',
        in_array($ext, ['ppt', 'pptx']) => 'bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400',
        in_array($ext, ['xls', 'xlsx']) => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400',
        default => 'bg-muted text-muted-foreground',
    };
@endphp

<div class="relative">
    {{-- ── Colourful hero band with hierarchy breadcrumb ── --}}
    <div class="relative overflow-hidden border-b border-border bg-card">
        {{-- Playful background decorations --}}
        <x-decorations.confetti-dots class="pointer-events-none absolute -right-6 -top-4 size-44 opacity-25" />
        <x-decorations.confetti-dots class="pointer-events-none absolute -left-8 bottom-0 size-36 rotate-90 opacity-20" />

        <div class="mx-auto max-w-6xl px-4 py-4">
            {{-- Back link --}}
            <div class="mb-3 flex items-center gap-2">
                <a href="{{ url('/brskanje') }}" class="flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span class="hidden sm:inline">Nazaj na iskanje</span>
                </a>
            </div>

            {{-- 4-level breadcrumb as coloured stepping stones --}}
            <nav aria-label="Hierarhija dokumenta" class="flex flex-wrap items-center gap-1.5">
                {{-- Level 1: Type --}}
                <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $tp['bg'] }} {{ $tp['text'] }} {{ $tp['border'] }}">
                    {{-- Layers icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                    </svg>
                    {{ $document->category?->name ?? 'Priprava' }}
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 text-muted-foreground/40">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>

                {{-- Level 2: School level --}}
                <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $lp['bg'] }} {{ $lp['text'] }} {{ $lp['border'] }}">
                    <span class="size-2 rounded-full {{ $lp['dot'] }}"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                    </svg>
                    {{ $document->schoolType?->name ?? '' }}
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 text-muted-foreground/40">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>

                {{-- Level 3: Grade --}}
                @if($document->grade)
                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $gradePalette['bg'] }} {{ $gradePalette['text'] }} {{ $gradePalette['border'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                        {{ $document->grade->name }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 text-muted-foreground/40">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                @endif

                {{-- Level 4: Subject --}}
                @if($document->subject)
                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $subjectPalette['bg'] }} {{ $subjectPalette['text'] }} {{ $subjectPalette['border'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        {{ $document->subject->name }}
                    </span>
                @endif
            </nav>
        </div>
    </div>

    {{-- ── Main content ── --}}
    <div class="relative mx-auto max-w-6xl px-4 py-8 md:py-12">
        {{-- Side decorations --}}
        <x-decorations.paperclip class="pointer-events-none absolute -left-1 top-32 hidden w-5 -rotate-12 opacity-20 lg:block xl:left-4" />
        <x-decorations.small-plant class="pointer-events-none absolute -right-2 bottom-16 hidden w-11 opacity-20 lg:block xl:right-4" />

        <div class="grid gap-8 lg:grid-cols-3">
            {{-- ── Left / main column ── --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Title hero card --}}
                <div class="relative overflow-hidden rounded-2xl border border-border bg-card p-6 md:p-8">
                    {{-- Sparkle decoration --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute right-4 top-4 size-5 text-amber-300 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>

                    {{-- Type badge --}}
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $tp['bg'] }} {{ $tp['text'] }} {{ $tp['border'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                        </svg>
                        {{ $document->category?->name ?? 'Priprava' }}
                    </span>

                    <h1 class="mt-3 text-balance font-serif text-2xl font-bold text-foreground md:text-3xl">
                        {{ $document->title }}
                    </h1>

                    @if($document->description)
                        <p class="mt-2 leading-relaxed text-muted-foreground">
                            {{ $document->description }}
                        </p>
                    @endif

                    {{-- Colourful hierarchy chips --}}
                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium {{ $lp['bg'] }} {{ $lp['text'] }} {{ $lp['border'] }}">
                            <span class="size-2 rounded-full {{ $lp['dot'] }}"></span>
                            {{ $document->schoolType?->name }}
                        </span>
                        @if($document->grade)
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium {{ $gradePalette['bg'] }} {{ $gradePalette['text'] }} {{ $gradePalette['border'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                {{ $document->grade->name }}
                            </span>
                        @endif
                        @if($document->subject)
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium {{ $subjectPalette['bg'] }} {{ $subjectPalette['text'] }} {{ $subjectPalette['border'] }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                {{ $document->subject->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Meta row --}}
                    <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-border pt-5 text-sm text-muted-foreground">
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span class="font-medium text-foreground">{{ $document->user?->display_name }}</span>
                            @if($authorBadge)
                                <x-badge-inline :badge="$authorBadge" />
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $document->created_at->format('d.m.Y H:i') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ number_format($document->views_count) }} ogledov
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            {{ number_format($document->downloads_count) }} prenosov
                        </span>
                    </div>

                    {{-- Tema detail --}}
                    @if($document->topic)
                        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800 dark:bg-amber-950/50">
                            <p class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Tema</p>
                            <p class="mt-0.5 text-sm text-amber-800 dark:text-amber-300">{{ $document->topic }}</p>
                        </div>
                    @endif
                </div>

                {{-- ── Files card ── --}}
                <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-sky-600 dark:text-sky-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-serif text-lg font-bold text-foreground">Datoteke v paketu</h2>
                            <p class="text-xs text-muted-foreground">{{ $document->files->count() }} {{ $document->files->count() === 1 ? 'datoteka' : 'datotek' }} v ZIP arhivu</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-2 sm:grid-cols-2">
                        @foreach($document->files as $file)
                            @if(auth()->check())
                                <a href="{{ route('document.download.file', [$document, $file]) }}"
                                   class="group flex items-center gap-3 rounded-xl border border-border bg-background p-3 transition-all hover:border-sky-200 hover:shadow-sm active:scale-[0.98]">
                            @else
                                <div class="group flex items-center gap-3 rounded-xl border border-border bg-background p-3 transition-all">
                            @endif
                                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $fileColor($file->extension) }}">
                                    @if(in_array($file->extension, ['jpg', 'jpeg', 'png']))
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M2.25 18V6a2.25 2.25 0 0 1 2.25-2.25h15A2.25 2.25 0 0 1 21.75 6v12A2.25 2.25 0 0 1 19.5 20.25H4.5A2.25 2.25 0 0 1 2.25 18Z" />
                                        </svg>
                                    @elseif(in_array($file->extension, ['doc', 'docx', 'pdf']))
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-foreground">{{ $file->original_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $file->human_size }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="hidden rounded border border-border px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground sm:inline-flex">
                                        {{ $file->extension }}
                                    </span>
                                    @auth
                                        <div class="flex size-8 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-600 transition-all group-hover:border-sky-300 group-hover:bg-sky-100 dark:border-sky-800 dark:bg-sky-950/50 dark:text-sky-400 dark:group-hover:border-sky-700 dark:group-hover:bg-sky-900/50" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </div>
                                    @endauth
                                </div>
                            @if(auth()->check())
                                </a>
                            @else
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @auth
                        <div class="mt-6">
                            <a href="{{ route('document.download.zip', $document) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-sky-700 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Prenesi vse datoteke (ZIP)
                            </a>
                        </div>
                    @endauth
                </div>

                {{-- ── Mobile-only: Action card ── --}}
                <div class="block rounded-2xl border border-border bg-card p-5 lg:hidden">
                    @auth
                        <a href="{{ route('document.download.zip', $document) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-4 text-base font-semibold text-white transition-colors hover:bg-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Prenesi pripravo
                        </a>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <livewire:document.save-button :document="$document" :is-saved="$isSaved" />
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                </svg>
                                Deli
                            </button>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                            <livewire:document.report-modal :document="$document" />
                        </div>

                        @if($isOwner)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <a href="{{ url('/dodajanje?uredi=' . $document->id) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm font-medium text-emerald-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 dark:border-emerald-800 dark:bg-background dark:text-emerald-300 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Uredi
                                </a>
                                <form method="POST" action="{{ route('document.destroy', $document) }}" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati dokument &quot;{{ $document->title }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-medium text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 dark:border-rose-800 dark:bg-background dark:text-rose-300 dark:hover:bg-rose-950/50 dark:hover:text-rose-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Izbriši
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-4 text-base font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Prijavite se za prenos
                        </a>
                        <p class="mt-2 text-center text-xs text-muted-foreground">
                            Še nimaš računa?
                            <a href="{{ route('register') }}" class="font-medium text-primary underline-offset-2 hover:underline">Registriraj se</a>
                        </p>

                        <div class="mt-4 border-t border-border pt-4">
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                </svg>
                                Deli
                            </button>
                        </div>
                    @endauth
                </div>

                {{-- ── Comments section (Livewire SFC) ── --}}
                <livewire:document.comment-section :document="$document" />

            </div>

            {{-- ── Right sidebar ── --}}
            <div class="space-y-5">

                {{-- Primary action card (desktop only) --}}
                <div class="hidden rounded-2xl border border-border bg-card p-5 lg:block">
                    @auth
                        <a href="{{ route('document.download.zip', $document) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-4 text-base font-semibold text-white transition-colors hover:bg-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Prenesi pripravo
                        </a>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <livewire:document.save-button :document="$document" :is-saved="$isSaved" context="desktop" />
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                </svg>
                                Deli
                            </button>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                            <livewire:document.report-modal :document="$document" context="desktop" />
                        </div>

                        @if($isOwner)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <a href="{{ url('/dodajanje?uredi=' . $document->id) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm font-medium text-emerald-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 dark:border-emerald-800 dark:bg-background dark:text-emerald-300 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Uredi
                                </a>
                                <form method="POST" action="{{ route('document.destroy', $document) }}" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati dokument &quot;{{ $document->title }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-medium text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 dark:border-rose-800 dark:bg-background dark:text-rose-300 dark:hover:bg-rose-950/50 dark:hover:text-rose-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Izbriši
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-4 text-base font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Prijavite se za prenos
                        </a>
                        <p class="mt-2 text-center text-xs text-muted-foreground">
                            Še nimaš računa?
                            <a href="{{ route('register') }}" class="font-medium text-primary underline-offset-2 hover:underline">Registriraj se</a>
                        </p>

                        <div class="mt-4 border-t border-border pt-4">
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                </svg>
                                Deli
                            </button>
                        </div>
                    @endauth
                </div>

                {{-- Rating card (Livewire SFC) --}}
                <livewire:document.rating-widget :document="$document" :user-rating="$userRating" />

                {{-- Author card --}}
                <div class="rounded-2xl border border-border bg-card p-5">
                    <h3 class="text-sm font-semibold text-foreground">Avtor</h3>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-100 to-emerald-100 dark:from-teal-900/50 dark:to-emerald-900/50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-teal-600 dark:text-teal-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="font-bold text-foreground">{{ $document->user?->display_name }}</p>
                                @if($authorBadge)
                                    <x-badge-inline :badge="$authorBadge" />
                                @endif
                            </div>
                            <p class="text-xs text-muted-foreground">Registriran uporabnik</p>
                        </div>
                    </div>
                    @if($document->user)
                        <a href="{{ url('/profil/' . $document->user->slug) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-border bg-background px-3 py-2 text-xs font-medium text-foreground transition-colors hover:bg-secondary">
                            Poglej profil
                        </a>
                    @endif
                </div>

                {{-- Hierarchy detail card – vertical colour-coded timeline --}}
                <div class="rounded-2xl border border-border bg-card p-5">
                    <h3 class="mb-5 text-sm font-semibold text-foreground">Razvrstitev</h3>
                    <div class="space-y-0">
                        @php
                            $hierarchyItems = [
                                ['label' => 'Tip gradiva', 'value' => $document->category?->name ?? 'Priprava', 'iconStyle' => $tp['icon'], 'pillBg' => $tp['bg'], 'pillText' => $tp['text'], 'pillBorder' => $tp['border'], 'iconType' => 'layers'],
                                ['label' => 'Stopnja', 'value' => $document->schoolType?->name ?? '', 'iconStyle' => $lp['icon'], 'pillBg' => $lp['bg'], 'pillText' => $lp['text'], 'pillBorder' => $lp['border'], 'iconType' => 'school'],
                            ];
                            if ($document->grade) {
                                $hierarchyItems[] = ['label' => 'Razred', 'value' => $document->grade->name, 'iconStyle' => $gradePalette['icon'], 'pillBg' => $gradePalette['bg'], 'pillText' => $gradePalette['text'], 'pillBorder' => $gradePalette['border'], 'iconType' => 'graduation'];
                            }
                            if ($document->subject) {
                                $hierarchyItems[] = ['label' => 'Predmet', 'value' => $document->subject->name, 'iconStyle' => $subjectPalette['icon'], 'pillBg' => $subjectPalette['bg'], 'pillText' => $subjectPalette['text'], 'pillBorder' => $subjectPalette['border'], 'iconType' => 'book'];
                            }
                        @endphp

                        @foreach($hierarchyItems as $index => $item)
                            <div class="flex items-start gap-3">
                                {{-- Vertical timeline --}}
                                <div class="relative flex flex-col items-center">
                                    <div class="flex size-9 items-center justify-center rounded-xl {{ $item['iconStyle'] }}">
                                        @if($item['iconType'] === 'layers')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                            </svg>
                                        @elseif($item['iconType'] === 'school')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                            </svg>
                                        @elseif($item['iconType'] === 'graduation')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                            </svg>
                                        @elseif($item['iconType'] === 'book')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        @endif
                                    </div>
                                    @if($index < count($hierarchyItems) - 1)
                                        <div class="my-0.5 h-5 w-0.5 rounded-full bg-border"></div>
                                    @endif
                                </div>
                                <div class="pb-2 pt-1.5">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">
                                        {{ $item['label'] }}
                                    </p>
                                    <span class="mt-0.5 inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold {{ $item['pillBg'] }} {{ $item['pillText'] }} {{ $item['pillBorder'] }}">
                                        {{ $item['value'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Related documents --}}
                @if($relatedDocuments->isNotEmpty())
                    <div class="rounded-2xl border border-border bg-card p-5">
                        <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-pink-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                            </svg>
                            Podobne priprave
                        </h3>
                        <div class="space-y-2">
                            @foreach($relatedDocuments as $rel)
                                @php
                                    $relCategorySlug = $rel->category?->slug ?? 'priprava';
                                    $relTp = $typePalette[$relCategorySlug] ?? $typePalette['priprava'];
                                    $relSchoolSlug = $rel->schoolType?->slug ?? 'os';
                                    $relLp = $levelPalette[$relSchoolSlug] ?? $levelPalette['os'];
                                @endphp
                                <a href="{{ route('document.show', $rel) }}" class="group block rounded-xl border border-border bg-background p-3 transition-all hover:border-pink-200 hover:shadow-sm">
                                    <div class="flex items-start gap-2">
                                        <div class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg {{ $relTp['icon'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-foreground transition-colors group-hover:text-pink-600">
                                                {{ $rel->title }}
                                            </p>
                                            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                                @if($rel->grade)
                                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $relLp['bg'] }} {{ $relLp['text'] }}">
                                                        {{ $rel->grade->name }}
                                                    </span>
                                                @endif
                                                @if($rel->subject)
                                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $subjectPalette['bg'] }} {{ $subjectPalette['text'] }}">
                                                        {{ $rel->subject->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1.5 flex items-center gap-1 text-xs text-muted-foreground">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                                {{ $rel->downloads_count }} prenosov
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

</x-layouts.app>
