<div class="space-y-2">
    {{-- ===== Stopnja (School Type) ===== --}}
    <div x-data="{ open: true }" class="border-b border-border pb-4">
        <button @click="open = !open" class="flex w-full items-center justify-between py-2">
            <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-teal-600">
                <x-icon-regular.school class="size-3.5" />
                Stopnja
            </span>
            <x-icon-regular.angle-down class="size-4 text-muted-foreground transition-transform" ::class="open && 'rotate-180'"/>
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
                            <x-dynamic-component :component="$conf['icon']" class="size-3.5" />
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
            <x-icon-regular.angle-down class="size-4 text-muted-foreground transition-transform" ::class="open && 'rotate-180'"/>
        </button>
        <div x-show="open" x-collapse class="mt-1">
            <div class="max-h-56 space-y-0.5 overflow-y-auto">
                @foreach($grades as $grade)
                    @php $gradeCount = $facetCounts['grade_id'][$grade->id] ?? 0; @endphp
                    @continue($hasFacetCounts && $gradeCount === 0 && $gradeId !== $grade->id)
                    <button
                        wire:click="setGrade({{ $gradeId === $grade->id ? 'null' : $grade->id }})"
                        @class([
                            'flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors',
                            'bg-indigo-500 font-semibold text-white' => $gradeId === $grade->id,
                            'text-muted-foreground hover:bg-indigo-50 hover:text-indigo-700 dark:hover:bg-indigo-950/50 dark:hover:text-indigo-300' => $gradeId !== $grade->id
                        ])>
                        <span class="flex items-center gap-2">
                            @if($gradeId === $grade->id)
                                <x-icon-regular.check class="size-3.5"/>
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
            <x-icon-regular.angle-down class="size-4 text-muted-foreground transition-transform" ::class="open && 'rotate-180'"/>
        </button>
        <div x-show="open" x-collapse class="mt-1">
            <div class="mb-2">
                <div class="relative">
                    <x-icon-regular.magnifying-glass class="absolute left-2.5 top-1/2 size-3.5 -translate-y-1/2 text-muted-foreground" />
                    <input
                        x-model="query"
                        type="search"
                        placeholder="Išči predmet..."
                        class="h-8 w-full rounded-lg border border-border bg-background pl-8 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-pink-300 focus:outline-none focus:ring-1 focus:ring-pink-200"
                    />
                </div>
            </div>
            <div x-ref="subjectList" class="max-h-52 space-y-0.5 overflow-y-auto">
                @forelse($subjects as $subject)
                    @php $subjectCount = $facetCounts['subject_id'][$subject->id] ?? 0; @endphp
                    @continue($hasFacetCounts && $subjectCount === 0 && $subjectId !== $subject->id)
                    <button
                        data-subject-item
                        x-show="matches($el)"
                        x-transition
                        wire:click="setSubject({{ $subjectId === $subject->id ? 'null' : $subject->id }})"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-1.5 text-sm transition-colors {{ $subjectId === $subject->id
                            ? 'bg-pink-500 font-semibold text-white'
                            : 'text-muted-foreground hover:bg-pink-50 hover:text-pink-700 dark:hover:bg-pink-950/50 dark:hover:text-pink-300'
                        }}"
                    >
                        <span class="flex items-center gap-2 truncate">
                            @if($subjectId === $subject->id)
                                <x-icon-regular.check class="size-3.5 shrink-0"/>
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
                    <p x-cloak
                        x-show="query !== '' && visibleCount === 0"
                        x-transition
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
            <x-icon-regular.angle-down class="size-4 text-muted-foreground transition-transform" ::class="open && 'rotate-180'"/>
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
                                <x-icon-regular.check class="size-3.5"/>
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
        <button wire:click="clearAllFilters"
            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 py-2.5 text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-100 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-300 dark:hover:bg-rose-950">
            <x-icon-regular.x class="size-3.5"/>
            Počisti vse filtre
        </button>
    @endif
</div>
