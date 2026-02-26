@php
    use App\Models\Category;
    use App\Models\Grade;
    use App\Models\SchoolType;
    use App\Models\Subject;

    $schoolTypeConfig = [
        'pv' => [
            'label' => 'Predšolska vzgoja',
            'shortLabel' => 'PV',
            'badge' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 dark:bg-fuchsia-950/50 dark:text-fuchsia-300 dark:border-fuchsia-800',
            'filterActive' => 'border-fuchsia-400 bg-fuchsia-500 text-white',
            'icon' => 'baby',
        ],
        'os' => [
            'label' => 'Osnovna šola',
            'shortLabel' => 'OS',
            'badge' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
            'filterActive' => 'border-teal-400 bg-teal-500 text-white',
            'icon' => 'school',
        ],
        'ss' => [
            'label' => 'Srednja šola',
            'shortLabel' => 'SS',
            'badge' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800',
            'filterActive' => 'border-orange-400 bg-orange-500 text-white',
            'icon' => 'graduation-cap',
        ],
    ];

    $categoryTypeStyles = [
        'priprava' => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800', 'abbr' => 'P'],
        'delovni-list' => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800', 'abbr' => 'DL'],
        'test' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'T'],
        'preverjanje-znanja' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'PZ'],
        'ucni-list' => ['badge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800', 'abbr' => 'UL'],
        'ostalo' => ['badge' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-950/50 dark:text-gray-300 dark:border-gray-800', 'abbr' => 'O'],
    ];

    $sortLabels = [
        'newest' => 'Najnovejše',
        'oldest' => 'Najstarejše',
        'most-downloaded' => 'Največ prenosov',
        'most-viewed' => 'Največ ogledov',
    ];

    $activeFilterCount = collect([$schoolTypeSlug, $gradeId, $subjectId])->filter()->count() + ($categoryIds !== [] ? 1 : 0);

    $selectedGrade = $gradeId ? $grades->firstWhere('id', $gradeId) : null;
    $selectedSubject = $subjectId ? $subjects->firstWhere('id', $subjectId) : null;
@endphp

<div class="relative min-h-[60vh]">
    {{-- ===== Search hero ===== --}}
    <div class="relative overflow-hidden border-b border-border bg-card">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <svg class="absolute inset-0 size-full">
                <defs>
                    <pattern id="browse-grid" x="0" y="0" width="28" height="28" patternUnits="userSpaceOnUse">
                        <path d="M 28 0 L 0 0 0 28" fill="none" stroke="currentColor" class="text-border" stroke-width="0.6" opacity="0.5" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#browse-grid)" />
            </svg>
            <div class="absolute inset-0 bg-gradient-to-r from-teal-50/30 via-transparent to-amber-50/20 dark:from-teal-950/20 dark:to-amber-950/10"></div>
        </div>

        <div class="relative mx-auto max-w-6xl px-4 py-8 md:py-10">
            <div class="mb-2 inline-flex items-center gap-1.5 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 dark:border-teal-800 dark:bg-teal-950/50">
                <x-icon-regular.magnifying-glass class="size-3.5 text-teal-500" />
                <span class="text-xs font-semibold text-teal-700 dark:text-teal-300">Išči med {{ number_format($totalHits, 0, ',', '.') }}+ pripravami</span>
            </div>
            <h1 class="font-serif text-2xl font-bold text-foreground md:text-3xl">
                Brskanje po pripravah
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Poiščite učne priprave, delovne liste, teste in več
            </p>

            {{-- Search bar --}}
            <div class="mt-5">
                <div class="relative max-w-2xl">
                    <div class="flex items-center overflow-hidden rounded-2xl border-2 border-teal-200 bg-card shadow-lg shadow-teal-100/40 transition-colors focus-within:border-teal-400 focus-within:shadow-teal-200/50 dark:shadow-teal-900/20">
                        <x-icon-regular.magnifying-glass class="ml-4 size-5 shrink-0 text-teal-500" />
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Išči priprave, npr. matematika 2. razred..."
                            class="h-12 flex-1 bg-transparent px-3 text-base text-foreground placeholder:text-muted-foreground focus:outline-none md:h-14"
                        />
                        @if($search)
                            <button
                                wire:click="$set('search', '')"
                                class="mr-1 flex size-8 items-center justify-center rounded-full text-muted-foreground hover:bg-muted hover:text-foreground"
                                aria-label="Počisti iskanje"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Main content: sidebar + results ===== --}}
    <div class="relative mx-auto max-w-6xl px-4 py-6 md:py-8">

        {{-- Active filter tags --}}
        @if($hasActiveFilters || $search !== '')
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-muted-foreground">Aktivni filtri:</span>

                @if($search !== '')
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-300">
                        &ldquo;{{ $search }}&rdquo;
                        <button wire:click="removeFilter('search')" class="ml-0.5 hover:text-teal-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </span>
                @endif

                @if($selectedSchoolType)
                    @php $stConf = $schoolTypeConfig[$selectedSchoolType->slug] ?? $schoolTypeConfig['os']; @endphp
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $stConf['badge'] }}">
                        {{ $stConf['label'] }}
                        <button wire:click="removeFilter('schoolTypeSlug')" class="ml-0.5 hover:opacity-70">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </span>
                @endif

                @if($selectedGrade)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-800 dark:bg-indigo-950/50 dark:text-indigo-300">
                        {{ $selectedGrade->name }}
                        <button wire:click="removeFilter('gradeId')" class="ml-0.5 hover:opacity-70">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </span>
                @endif

                @if($selectedSubject)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-pink-200 bg-pink-50 px-3 py-1 text-xs font-semibold text-pink-700 dark:border-pink-800 dark:bg-pink-950/50 dark:text-pink-300">
                        {{ $selectedSubject->name }}
                        <button wire:click="removeFilter('subjectId')" class="ml-0.5 hover:opacity-70">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </span>
                @endif

                @foreach($selectedCategories as $selCat)
                    @php $catStyle = $categoryTypeStyles[$selCat->slug] ?? $categoryTypeStyles['priprava']; @endphp
                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $catStyle['badge'] }}">
                        {{ $selCat->name }}
                        <button wire:click="toggleCategory({{ $selCat->id }})" class="ml-0.5 hover:opacity-70">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </span>
                @endforeach

                @if($activeFilterCount > 1 || ($activeFilterCount > 0 && $search !== ''))
                    <button wire:click="clearAllFilters" class="text-xs font-medium text-rose-500 hover:text-rose-700">
                        Počisti vse
                    </button>
                @endif
            </div>
        @endif

        <div class="flex gap-6">
            {{-- ===== Left sidebar (desktop) ===== --}}
            <aside class="hidden w-64 shrink-0 lg:block">
                <div class="sticky top-20 rounded-2xl border border-border bg-card p-4">
                    <div class="mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-muted-foreground"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                        <span class="text-sm font-bold text-foreground">Filtri</span>
                    </div>
                    @include('livewire.partials.browse-filters')
                </div>
            </aside>

            {{-- ===== Right: results ===== --}}
            <div class="min-w-0 flex-1">
                {{-- Mobile filter toggle + sort --}}
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        {{-- Mobile filters button --}}
                        <button
                            x-data
                            @click="$dispatch('open-mobile-filters')"
                            class="flex items-center gap-1.5 rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground lg:hidden"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                            Filtri
                            @if($activeFilterCount > 0)
                                <span class="flex size-5 items-center justify-center rounded-full bg-teal-500 text-[10px] font-bold text-white">
                                    {{ $activeFilterCount }}
                                </span>
                            @endif
                        </button>

                        <div class="flex items-center gap-2">
                            <x-icon-regular.sparkles class="size-4 text-amber-500" />
                            <span class="text-sm font-semibold text-foreground">
                                {{ number_format($totalHits, 0, ',', '.') }}
                                {{ match(true) {
                                    $totalHits === 1 => 'rezultat',
                                    $totalHits === 2 => 'rezultata',
                                    $totalHits <= 4 => 'rezultati',
                                    default => 'rezultatov',
                                } }}
                            </span>
                        </div>
                    </div>

                    {{-- Sort dropdown --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-semibold text-muted-foreground transition-colors hover:text-foreground"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" /></svg>
                            {{ $sortLabels[$sort] ?? $sortLabels['newest'] }}
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 top-full z-10 mt-1 w-48 overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                            @foreach($sortLabels as $key => $label)
                                <button
                                    wire:click="$set('sort', '{{ $key }}')"
                                    @click="open = false"
                                    class="block w-full px-4 py-2.5 text-left text-sm transition-colors hover:bg-secondary {{ $sort === $key ? 'bg-secondary font-semibold text-foreground' : 'text-muted-foreground' }}"
                                >
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Results list --}}
                <div class="space-y-2" wire:loading.class="opacity-50">
                    @forelse($documents as $document)
                        <x-document-row :document="$document" />
                    @empty
                        <div class="rounded-2xl border-2 border-dashed border-muted bg-background py-16 text-center">
                            <x-icon-regular.file-lines class="mx-auto size-10 text-muted-foreground/40" />
                            <p class="mt-3 font-serif text-lg font-semibold text-muted-foreground">
                                Ni najdenih priprav
                            </p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Poskusite spremeniti filtre ali iskalni niz
                            </p>
                            @if($hasActiveFilters || $search !== '')
                                <button
                                    wire:click="clearAllFilters"
                                    class="mt-4 rounded-xl border border-teal-200 bg-teal-50 px-5 py-2 text-sm font-semibold text-teal-700 transition-colors hover:bg-teal-100"
                                >
                                    Počisti vse filtre
                                </button>
                            @endif
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($totalPages > 1)
                    <div class="mt-8 flex items-center justify-center gap-2">
                        <button
                            wire:click="setPage({{ max(1, $currentPage - 1) }})"
                            @disabled($currentPage === 1)
                            class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground disabled:opacity-40 disabled:hover:border-border"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        </button>

                        @php
                            // Show max 7 page buttons with ellipsis
                            $startPage = max(1, $currentPage - 3);
                            $endPage = min($totalPages, $startPage + 6);
                            $startPage = max(1, $endPage - 6);
                        @endphp

                        @if($startPage > 1)
                            <button wire:click="setPage(1)" class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-sm font-semibold text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground">1</button>
                            @if($startPage > 2)
                                <span class="px-1 text-muted-foreground">&hellip;</span>
                            @endif
                        @endif

                        @for($p = $startPage; $p <= $endPage; $p++)
                            <button
                                wire:click="setPage({{ $p }})"
                                class="flex size-10 items-center justify-center rounded-xl border text-sm font-semibold transition-all {{ $p === $currentPage
                                    ? 'border-teal-400 bg-teal-500 text-white shadow-md shadow-teal-200/50 dark:shadow-teal-900/30'
                                    : 'border-border bg-card text-muted-foreground hover:border-teal-200 hover:text-foreground'
                                }}"
                            >
                                {{ $p }}
                            </button>
                        @endfor

                        @if($endPage < $totalPages)
                            @if($endPage < $totalPages - 1)
                                <span class="px-1 text-muted-foreground">&hellip;</span>
                            @endif
                            <button wire:click="setPage({{ $totalPages }})" class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-sm font-semibold text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground">{{ $totalPages }}</button>
                        @endif

                        <button
                            wire:click="setPage({{ min($totalPages, $currentPage + 1) }})"
                            @disabled($currentPage === $totalPages)
                            class="flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground transition-all hover:border-teal-200 hover:text-foreground disabled:opacity-40 disabled:hover:border-border"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                        </button>
                    </div>

                    <p class="mt-3 text-center text-xs text-muted-foreground">
                        Stran {{ $currentPage }} od {{ $totalPages }} &middot; Prikazanih {{ $documents->count() }} od {{ number_format($totalHits, 0, ',', '.') }} rezultatov
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Mobile filter drawer ===== --}}
    <div
        x-data="{ open: false }"
        @open-mobile-filters.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
    >
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
            class="absolute inset-0 bg-black/40"
        ></div>

        {{-- Drawer --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute bottom-0 left-0 right-0 max-h-[85vh] overflow-y-auto rounded-t-2xl border-t border-border bg-card p-5 shadow-2xl"
        >
            <div class="mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2 text-base font-bold text-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    Filtri
                </span>
                <button
                    @click="open = false"
                    class="flex size-9 items-center justify-center rounded-full bg-secondary text-muted-foreground hover:text-foreground"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>

            @include('livewire.partials.browse-filters')

            <div class="mt-4 grid grid-cols-2 gap-2">
                <button
                    wire:click="clearAllFilters"
                    @click="open = false"
                    class="rounded-xl border border-border py-2.5 text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground"
                >
                    Počisti
                </button>
                <button
                    @click="open = false"
                    class="rounded-xl bg-teal-500 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-teal-600"
                >
                    Pokaži rezultate ({{ number_format($totalHits, 0, ',', '.') }})
                </button>
            </div>
        </div>
    </div>
</div>
