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

    $schoolTypeConfig = \App\Support\SchoolTypeUiConfig::all();
    $schoolTypeSlug = $document->schoolType?->slug ?? 'os';
    $lp = ($schoolTypeConfig[$schoolTypeSlug] ?? $schoolTypeConfig['os'])['level'];

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
                <a href="{{ route('browse') }}" class="flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
                    <x-icon-regular.arrow-left class="size-4" />
                    <span class="hidden sm:inline">Nazaj na iskanje</span>
                </a>
            </div>

            {{-- 4-level breadcrumb as coloured stepping stones --}}
            <nav aria-label="Hierarhija dokumenta" class="flex flex-wrap items-center gap-1.5">
                {{-- Level 1: Type --}}
                <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $tp['bg'] }} {{ $tp['text'] }} {{ $tp['border'] }}">
                    <x-icon-regular.layer-group class="size-3.5" />
                    {{ $document->category?->name ?? 'Priprava' }}
                </span>
                <x-icon-regular.angle-right class="size-3.5 text-muted-foreground/40" />

                {{-- Level 2: School level --}}
                <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $lp['bg'] }} {{ $lp['text'] }} {{ $lp['border'] }}">
                    <span class="size-2 rounded-full {{ $lp['dot'] }}"></span>
                    <x-icon-regular.school class="size-3.5" />
                    {{ $document->schoolType?->name ?? '' }}
                </span>
                <x-icon-regular.angle-right class="size-3.5 text-muted-foreground/40" />

                {{-- Level 3: Grade --}}
                @if($document->grade)
                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $gradePalette['bg'] }} {{ $gradePalette['text'] }} {{ $gradePalette['border'] }}">
                        <x-icon-regular.graduation-cap class="size-3.5" />
                        {{ $document->grade->name }}
                    </span>
                    <x-icon-regular.angle-right class="size-3.5 text-muted-foreground/40" />
                @endif

                {{-- Level 4: Subject --}}
                @if($document->subject)
                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $subjectPalette['bg'] }} {{ $subjectPalette['text'] }} {{ $subjectPalette['border'] }}">
                        <x-icon-regular.book-open class="size-3.5" />
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
                    <x-icon-regular.sparkles class="absolute right-4 top-4 size-5 text-amber-300 opacity-60" />

                    {{-- Type badge --}}
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $tp['bg'] }} {{ $tp['text'] }} {{ $tp['border'] }}">
                        <x-icon-regular.layer-group class="size-3.5" />
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
                                <x-icon-regular.graduation-cap class="size-3.5" />
                                {{ $document->grade->name }}
                            </span>
                        @endif
                        @if($document->subject)
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium {{ $subjectPalette['bg'] }} {{ $subjectPalette['text'] }} {{ $subjectPalette['border'] }}">
                                <x-icon-regular.book-open class="size-3.5" />
                                {{ $document->subject->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Meta row --}}
                    <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-border pt-5 text-sm text-muted-foreground">
                        <span class="flex items-center gap-1.5">
                            <x-icon-regular.user class="size-4" />
                            <span class="font-medium text-foreground">{{ $document->user?->display_name }}</span>
                            @if($authorBadge)
                                <x-badge-inline :badge="$authorBadge" />
                            @endif
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-icon-regular.clock class="size-4" />
                            {{ $document->created_at->format('d.m.Y H:i') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-icon-regular.eye class="size-4" />
                            {{ number_format($document->views_count) }} ogledov
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-icon-regular.download class="size-4" />
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
                            <x-icon-regular.files class="size-4 text-sky-600 dark:text-sky-400" />
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
                                    <x-dynamic-component  :component="'icon-regular.' . $file->icon" class="size-5" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-foreground">{{ $file->original_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ \Illuminate\Support\Number::fileSize($file->size_bytes ) }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <span class="hidden rounded border border-border px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground sm:inline-flex">
                                        {{ $file->extension }}
                                    </span>
                                    @auth
                                        <div class="flex size-8 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-600 transition-all group-hover:border-sky-300 group-hover:bg-sky-100 dark:border-sky-800 dark:bg-sky-950/50 dark:text-sky-400 dark:group-hover:border-sky-700 dark:group-hover:bg-sky-900/50" aria-hidden="true">
                                            <x-icon-regular.download class="size-4" />
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
                                <x-icon-regular.download class="size-5" />
                                Prenesi vse datoteke (ZIP)
                            </a>
                        </div>
                    @endauth
                </div>

                {{-- ── Mobile-only: Action card ── --}}
                <div class="block rounded-2xl border border-border bg-card p-5 lg:hidden">
                    @auth
                        <a href="{{ route('document.download.zip', $document) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-4 text-base font-semibold text-white transition-colors hover:bg-emerald-700">
                            <x-icon-regular.download class="size-6" />
                            Prenesi pripravo
                        </a>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <livewire:document.save-button :$document :is-saved="$isSaved" />
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <x-icon-regular.share-nodes class="size-5" />
                                Deli
                            </button>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                            <livewire:document.report-modal :$document />
                        </div>

                        @if($isOwner)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <a href="{{ url('/dodajanje?uredi=' . $document->id) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm font-medium text-emerald-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 dark:border-emerald-800 dark:bg-background dark:text-emerald-300 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-200">
                                    <x-icon-regular.pen-to-square class="size-4" />
                                    Uredi
                                </a>
                                <form method="POST" action="{{ route('document.destroy', $document) }}" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati dokument &quot;{{ $document->title }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-medium text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 dark:border-rose-800 dark:bg-background dark:text-rose-300 dark:hover:bg-rose-950/50 dark:hover:text-rose-200">
                                        <x-icon-regular.trash-can class="size-4" />
                                        Izbriši
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-4 text-base font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                            <x-icon-regular.arrow-right-from-bracket class="size-5" />
                            Prijavite se za prenos
                        </a>
                        <p class="mt-2 text-center text-xs text-muted-foreground">
                            Še nimaš računa?
                            <a href="{{ route('register') }}" class="font-medium text-primary underline-offset-2 hover:underline">Registriraj se</a>
                        </p>

                        <div class="mt-4 border-t border-border pt-4">
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <x-icon-regular.share-nodes class="size-5" />
                                Deli
                            </button>
                        </div>
                    @endauth
                </div>

                {{-- ── Comments section (Livewire SFC) ── --}}
                <livewire:document.comment-section :$document />
            </div>

            {{-- ── Right sidebar ── --}}
            <div class="space-y-5">

                {{-- Primary action card (desktop only) --}}
                <div class="hidden rounded-2xl border border-border bg-card p-5 lg:block">
                    @auth
                        <a href="{{ route('document.download.zip', $document) }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-4 text-base font-semibold text-white transition-colors hover:bg-emerald-700">
                            <x-icon-regular.download class="size-5" />
                            Prenesi pripravo
                        </a>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <livewire:document.save-button :$document :is-saved="$isSaved" context="desktop" />
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <x-icon-regular.share-nodes class="size-4" />
                                Deli
                            </button>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-border pt-4">
                            <livewire:document.report-modal :$document context="desktop" />
                        </div>

                        @if($isOwner)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                <a href="{{ url('/dodajanje?uredi=' . $document->id) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm font-medium text-emerald-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 dark:border-emerald-800 dark:bg-background dark:text-emerald-300 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-200">
                                    <x-icon-regular.pen-to-square class="size-4" />
                                    Uredi
                                </a>
                                <form method="POST" action="{{ route('document.destroy', $document) }}" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati dokument &quot;{{ $document->title }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-medium text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800 dark:border-rose-800 dark:bg-background dark:text-rose-300 dark:hover:bg-rose-950/50 dark:hover:text-rose-200">
                                        <x-icon-regular.trash-can class="size-4" />
                                        Izbriši
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-4 text-base font-semibold text-primary-foreground transition-colors hover:bg-primary/90">
                            <x-icon-regular.download class="size-5" />
                            Prijavite se za prenos
                        </a>
                        <p class="mt-2 text-center text-xs text-muted-foreground">
                            Še nimaš računa?
                            <a href="{{ route('register') }}" class="font-medium text-primary underline-offset-2 hover:underline">Registriraj se</a>
                        </p>

                        <div class="mt-4 border-t border-border pt-4">
                            <button onclick="navigator.share?.({ title: '{{ $document->title }}', url: window.location.href }) ?? navigator.clipboard.writeText(window.location.href).then(() => alert('Povezava kopirana!'))"
                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                                <x-icon-regular.share-nodes class="size-4" />
                                Deli
                            </button>
                        </div>
                    @endauth
                </div>

                {{-- Rating card (Livewire SFC) --}}
                <livewire:document.rating-widget :$document :user-rating="$userRating" />

                {{-- Author card --}}
                <div class="rounded-2xl border border-border bg-card p-5">
                    <h3 class="text-sm font-semibold text-foreground">Avtor</h3>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex size-14 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-100 to-emerald-100 dark:from-teal-900/50 dark:to-emerald-900/50">
                            <x-icon-regular.user class="size-7 text-teal-600 dark:text-teal-400" />
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

                    <flux:button href="{{ route('profile.show', $document->user) }}" class="mt-4 w-full text-xs" size="sm">
                        Poglej profil
                    </flux:button>
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
                                            <x-icon-regular.layer-group class="size-4" />
                                        @elseif($item['iconType'] === 'school')
                                            <x-icon-regular.school class="size-4" />
                                        @elseif($item['iconType'] === 'graduation')
                                            <x-icon-regular.graduation-cap class="size-4" />
                                        @elseif($item['iconType'] === 'book')
                                            <x-icon-regular.book-open class="size-4" />
                                        @endif
                                    </div>
                                    @if($index < count($hierarchyItems) - 1)
                                        <div class="my-0.5 h-5 w-0.5 rounded-full bg-border"></div>
                                    @endif
                                </div>
                                <div class="pb-2 pt-1">
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
                            <x-icon-regular.sparkles class="size-4 text-pink-400"/>
                            Podobne priprave
                        </h3>
                        <div class="space-y-2">
                            @foreach($relatedDocuments as $rel)
                                @php
                                    $relCategorySlug = $rel->category?->slug ?? 'priprava';
                                    $relTp = $typePalette[$relCategorySlug] ?? $typePalette['priprava'];
                                    $relSchoolSlug = $rel->schoolType?->slug ?? 'os';
                                    $relLp = ($schoolTypeConfig[$relSchoolSlug] ?? $schoolTypeConfig['os'])['level'];
                                @endphp
                                <a href="{{ route('document.show', $rel) }}" class="group block rounded-xl border border-border bg-background p-3 transition-all hover:border-pink-200 hover:shadow-sm">
                                    <div class="flex items-start gap-2">
                                        <div class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg {{ $relTp['icon'] }}">
                                            <x-icon-regular.layer-group  class="size-3.5" />
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
                                                <x-icon-regular.download class="size-3" />
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
