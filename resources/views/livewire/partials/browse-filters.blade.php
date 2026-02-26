@php
    use App\Models\SchoolType;
    use App\Models\Grade;
    use App\Models\Subject;
    use App\Models\Category;

    $schoolTypeConfig = [
        'pv' => [
            'label' => 'Predšolska vzgoja',
            'filterActive' => 'border-fuchsia-400 bg-fuchsia-500 text-white',
            'icon' => 'baby',
        ],
        'os' => [
            'label' => 'Osnovna šola',
            'filterActive' => 'border-teal-400 bg-teal-500 text-white',
            'icon' => 'school',
        ],
        'ss' => [
            'label' => 'Srednja šola',
            'filterActive' => 'border-orange-400 bg-orange-500 text-white',
            'icon' => 'graduation-cap',
        ],
    ];

    $schoolTypeIcons = [
        'baby' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>',
        'school' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a23.838 23.838 0 0 0-1.012 5.434c3.178.534 6.26 1.46 9.243 2.724a52.378 52.378 0 0 1 9.243-2.724 23.836 23.836 0 0 0-1.012-5.434m-15.482 0A47.38 47.38 0 0 1 12 5.683a47.38 47.38 0 0 1 7.74 4.464m-15.482 0a65.46 65.46 0 0 0-3.21 1.39A59.963 59.963 0 0 1 12 3.493a59.965 59.965 0 0 1 11.952 6.09 65.46 65.46 0 0 0-3.21-1.39" /></svg>',
        'graduation-cap' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a23.838 23.838 0 0 0-1.012 5.434c3.178.534 6.26 1.46 9.243 2.724a52.378 52.378 0 0 1 9.243-2.724 23.836 23.836 0 0 0-1.012-5.434m-15.482 0A47.38 47.38 0 0 1 12 5.683a47.38 47.38 0 0 1 7.74 4.464" /></svg>',
    ];

    $categoryTypeStyles = [
        'priprava' => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800', 'abbr' => 'P'],
        'delovni-list' => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800', 'abbr' => 'DL'],
        'test' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'T'],
        'preverjanje-znanja' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'PZ'],
        'ucni-list' => ['badge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800', 'abbr' => 'UL'],
        'ostalo' => ['badge' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-950/50 dark:text-gray-300 dark:border-gray-800', 'abbr' => 'O'],
    ];
@endphp

<div class="space-y-2">
    {{-- ===== Stopnja (School Type) ===== --}}
    <div x-data="{ open: true }" class="border-b border-border pb-4">
        <button @click="open = !open" class="flex w-full items-center justify-between py-2">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-teal-600">
                <x-icon-regular.school class="size-3.5" />
                Stopnja
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-muted-foreground transition-transform" :class="open && 'rotate-180'"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </button>
        <div x-show="open" x-collapse class="mt-1">
            <div class="space-y-0.5">
                <button
                    wire:click="setSchoolType(null)"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ $selectedSchoolType === null ? 'bg-foreground font-semibold text-background' : 'text-muted-foreground hover:bg-secondary hover:text-foreground' }}"
                >
                    <span>Vse stopnje</span>
                </button>

                @foreach($schoolTypes as $st)
                    @php $conf = $schoolTypeConfig[$st->slug] ?? $schoolTypeConfig['os']; @endphp
                    <button
                        wire:click="setSchoolType('{{ $st->slug }}')"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ $selectedSchoolType?->is($st) ? $conf['filterActive'] . ' font-semibold' : 'text-muted-foreground hover:bg-secondary hover:text-foreground' }}"
                    >
                        <span class="flex items-center gap-2">
                            {!! $schoolTypeIcons[$conf['icon']] !!}
                            {{ $conf['label'] }}
                        </span>
                        @if(!empty($facetCounts['school_type_id'][$st->id]))
                            <span class="text-xs opacity-70">{{ $facetCounts['school_type_id'][$st->id] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== Razred (Grade) - always visible ===== --}}
    <div x-data="{ open: true }" class="border-b border-border pb-4">
        <button @click="open = !open" class="flex w-full items-center justify-between py-2">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-600">
                <x-icon-regular.graduation-cap class="size-3.5" />
                Razred
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-muted-foreground transition-transform" :class="open && 'rotate-180'"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </button>
        <div x-show="open" x-collapse class="mt-1">
            <div class="max-h-56 space-y-0.5 overflow-y-auto">
                @foreach($grades as $grade)
                    @php $gradeCount = $facetCounts['grade_id'][$grade->id] ?? 0; @endphp
                    <button
                        wire:click="setGrade({{ $gradeId === $grade->id ? 'null' : $grade->id }})"
                        @if($hasFacetCounts && $gradeCount === 0 && $gradeId !== $grade->id) disabled @endif
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ $gradeId === $grade->id
                            ? 'bg-indigo-500 font-semibold text-white'
                            : ($hasFacetCounts && $gradeCount === 0
                                ? 'text-muted-foreground/50 cursor-default'
                                : 'text-muted-foreground hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/50 dark:hover:text-indigo-300')
                        }}"
                    >
                        <span class="flex items-center gap-2">
                            @if($gradeId === $grade->id)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @endif
                            {{ $grade->name }}
                        </span>
                        @if($gradeCount > 0)
                            <span class="text-xs opacity-70">{{ $gradeCount }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== Predmet (Subject) - with search ===== --}}
    <div
        x-data="{
            open: true,
            query: '',
            visibleCount: {{ $subjects->count() }},
            normalize(value) {
                const characterMap = {
                    'đ': 'd',
                    'ð': 'd',
                    'ø': 'o',
                    'ł': 'l',
                    'ß': 'ss',
                    'æ': 'ae',
                    'œ': 'oe',
                };

                const mapped = [...String(value ?? '').toLowerCase()]
                    .map((character) => characterMap[character] ?? character)
                    .join('');

                return mapped
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');
            },
            matches(element) {
                const query = this.normalize(this.query);

                if (query === '') {
                    return true;
                }

                const subjectName = this.normalize(
                    element.querySelector('[data-subject-name]')?.textContent ?? ''
                );

                return subjectName.includes(query);
            },
            updateVisibleCount() {
                const items = this.$refs.subjectList?.querySelectorAll('[data-subject-item]') ?? [];
                this.visibleCount = [...items].filter((item) => this.matches(item)).length;
            },
        }"
        x-init="$nextTick(() => updateVisibleCount())"
        x-effect="query; updateVisibleCount()"
        class="border-b border-border pb-4"
    >
        <button @click="open = !open" class="flex w-full items-center justify-between py-2">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-pink-600">
                <x-icon-regular.book-open class="size-3.5" />
                Predmet
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-muted-foreground transition-transform" :class="open && 'rotate-180'"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </button>
        <div x-show="open" x-collapse class="mt-1">
            <div class="mb-2">
                <div class="relative">
                    <x-icon-regular.magnifying-glass class="absolute left-2.5 top-1/2 size-3.5 -translate-y-1/2 text-muted-foreground" />
                    <input
                        x-model="query"
                        type="text"
                        placeholder="Išči predmet..."
                        class="h-8 w-full rounded-lg border border-border bg-background pl-8 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-pink-300 focus:outline-none focus:ring-1 focus:ring-pink-200"
                    />
                </div>
            </div>
            <div x-ref="subjectList" class="max-h-52 space-y-0.5 overflow-y-auto">
                @forelse($subjects as $subject)
                    @php $subjectCount = $facetCounts['subject_id'][$subject->id] ?? 0; @endphp
                    <button
                        data-subject-item
                        x-show="matches($el)"
                        x-transition
                        wire:click="setSubject({{ $subjectId === $subject->id ? 'null' : $subject->id }})"
                        @if($hasFacetCounts && $subjectCount === 0 && $subjectId !== $subject->id) disabled @endif
                        class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-sm transition-colors {{ $subjectId === $subject->id
                            ? 'bg-pink-500 font-semibold text-white'
                            : ($hasFacetCounts && $subjectCount === 0
                                ? 'text-muted-foreground/50 cursor-default'
                                : 'text-muted-foreground hover:bg-pink-50 hover:text-pink-700 dark:hover:bg-pink-950/50 dark:hover:text-pink-300')
                        }}"
                    >
                        <span class="flex items-center gap-2 truncate">
                            @if($subjectId === $subject->id)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @endif
                            <span data-subject-name class="truncate">{{ $subject->name }}</span>
                        </span>
                        @if($subjectCount > 0)
                            <span class="ml-2 shrink-0 text-xs opacity-70">{{ $subjectCount }}</span>
                        @endif
                    </button>
                @empty
                    <p class="px-3 py-2 text-xs text-muted-foreground">Ni zadetkov</p>
                @endforelse
                @if($subjects->isNotEmpty())
                    <p
                        x-cloak
                        x-show="query !== '' && visibleCount === 0"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="px-3 py-2 text-xs text-muted-foreground"
                    >Ni zadetkov</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Tip gradiva (Category / Document Type) - multiselect ===== --}}
    <div x-data="{ open: true }" class="border-b border-border pb-4">
        <button @click="open = !open" class="flex w-full items-center justify-between py-2">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-600">
                <x-icon-regular.layer-group class="size-3.5" />
                Tip gradiva
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-muted-foreground transition-transform" :class="open && 'rotate-180'"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </button>
        <div x-show="open" x-collapse class="mt-1">
            <div class="space-y-0.5">
                @foreach($allCategories as $category)
                    @php
                        $catStyle = $categoryTypeStyles[$category->slug] ?? $categoryTypeStyles['priprava'];
                        $catCount = $facetCounts['category_id'][$category->id] ?? 0;
                        $isSelected = in_array($category->id, $categoryIds);
                    @endphp
                    <button
                        wire:click="toggleCategory({{ $category->id }})"
                        @if($hasFacetCounts && $catCount === 0 && !$isSelected) disabled @endif
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ $isSelected
                            ? 'bg-emerald-500 font-semibold text-white'
                            : ($hasFacetCounts && $catCount === 0
                                ? 'text-muted-foreground/50 cursor-default'
                                : 'text-muted-foreground hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-300')
                        }}"
                    >
                        <span class="flex items-center gap-2">
                            @if($isSelected)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            @endif
                            <span class="flex size-6 items-center justify-center rounded text-[10px] font-bold {{ $isSelected ? 'bg-white/20 text-white' : $catStyle['badge'] }}">
                                {{ $catStyle['abbr'] }}
                            </span>
                            {{ $category->name }}
                        </span>
                        @if($catCount > 0)
                            <span class="text-xs opacity-70">{{ $catCount }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Clear all filters --}}
    @if($hasActiveFilters)
        <button
            wire:click="clearAllFilters"
            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 py-2.5 text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-100 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-300 dark:hover:bg-rose-950"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            Počisti vse filtre
        </button>
    @endif
</div>
