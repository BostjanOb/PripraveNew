<div class="relative">

    @if($submitted)
        {{-- ── Success screen ── --}}
        <div class="mx-auto max-w-2xl px-4 py-20 text-center">
            <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/40">
                {{-- CheckCircle2 --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-emerald-600 dark:text-emerald-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1 class="font-serif text-3xl font-bold text-foreground">Gradivo uspešno dodano!</h1>
            <p class="mt-3 text-base text-muted-foreground">
                Vaše gradivo je bilo uspešno naloženo in bo kmalu dostopno drugim uporabnikom.
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <flux:button wire:click="resetForm" variant="outline" class="gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    Dodaj novo gradivo
                </flux:button>
                <flux:button as="a" href="{{ route('home') }}" class="gap-1.5 bg-teal-500 text-white hover:bg-teal-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    Na brskanje
                </flux:button>
            </div>
        </div>
    @else
        {{-- ── Hero ── --}}
        <div class="relative overflow-hidden border-b border-border bg-card">
            <svg class="pointer-events-none absolute -left-6 -top-2 size-48 opacity-30" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/><circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/><circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/><circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/><circle cx="180" cy="30" r="4" fill="#0ea5e9" opacity=".6"/><circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/><circle cx="80" cy="55" r="4" fill="#10b981" opacity=".4"/><circle cx="125" cy="65" r="3" fill="#f59e0b" opacity=".5"/><circle cx="165" cy="50" r="5" fill="#6366f1" opacity=".4"/><circle cx="15" cy="100" r="4" fill="#ec4899" opacity=".5"/>
            </svg>
            <svg class="pointer-events-none absolute -right-4 bottom-0 size-36 rotate-180 opacity-20" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/><circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/><circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/><circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/><circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/>
            </svg>

            <div class="relative mx-auto max-w-4xl px-4 py-10 text-center md:py-12">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/50">
                    <x-icon-regular.upload  class="size-9 text-emerald-600 dark:text-emerald-300" />
                </div>
                <h1 class="font-serif text-3xl font-bold text-foreground md:text-4xl">Dodajanje gradiva</h1>
                <p class="mt-3 text-base text-muted-foreground md:text-lg">
                    Čim natančneje izpolnite spodnja polja in s tem omogočite lažje iskanje gradiv.
                </p>
            </div>
        </div>

        {{-- ── Form ── --}}
        <form
            wire:submit="submit"
            x-data="{
                categoryType: $wire.entangle('categoryType'),
                schoolTypeId: '',
                gradeId: '',
                allGrades: @js($grades->map(fn($g) => ['id' => $g->id, 'name' => $g->name, 'school_type_id' => $g->school_type_id])),
                get filteredGrades() {
                    return this.allGrades.filter(g => g.school_type_id == this.schoolTypeId);
                },
                selectSchoolType(id) {
                    this.schoolTypeId = id;
                    this.gradeId = '';
                    $wire.set('schoolTypeId', String(id));
                    $wire.set('gradeId', '');
                    $wire.set('subjectId', '');
                    $wire.set('subjectSearch', '');
                },
                selectGrade(id) {
                    this.gradeId = id;
                    $wire.set('gradeId', String(id));
                },
            }"
            class="mx-auto max-w-4xl space-y-8 px-4 py-8 md:py-12"
        >

            {{-- ── Section 1: Vrsta gradiva ── --}}
            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50">
                        <x-icon-regular.layer-group  class="size-4 text-emerald-600 dark:text-emerald-300"/>
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">Vrsta gradiva</h2>
                </div>

                {{-- Info callout --}}
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-800 dark:bg-sky-950/40">
                    <x-icon-regular.circle-info  class="mt-0.5 size-5 shrink-0 text-sky-500 dark:text-sky-300"/>
                    <p class="text-sm text-sky-700 dark:text-sky-200">
                        Pri določitvi vrste gradiva bodite pozorni: <strong>PRIPRAVA</strong> zajema dnevne, tedenske in letne priprave, medtem ko <strong>OSTALO</strong> vključuje učne liste, preverjanja, predstavitve in podobno.
                    </p>
                </div>

                {{-- Document type cards --}}
                <div class="grid gap-3 sm:grid-cols-2">
                    <button
                        type="button"
                        @click="categoryType = 'priprava'; $wire.set('categoryType', 'priprava'); $wire.set('ostaloCategory', '')"
                        :class="categoryType === 'priprava'
                            ? 'border-emerald-400 bg-emerald-50 shadow-md shadow-emerald-100/50 dark:border-emerald-600 dark:bg-emerald-950/40 dark:shadow-emerald-950/40'
                            : 'border-border bg-background hover:border-emerald-200 hover:shadow-sm dark:hover:border-emerald-800'"
                        class="flex items-center justify-between rounded-xl border-2 p-4 text-left transition-all"
                    >
                        <div>
                            <span :class="categoryType === 'priprava' ? 'text-emerald-700 dark:text-emerald-300' : 'text-foreground'" class="text-sm font-bold">Priprava</span>
                            <span class="mt-0.5 block text-xs text-muted-foreground">Dnevna, tedenska, letna priprava</span>
                        </div>
                        <div
                            :class="categoryType === 'priprava'
                                ? 'bg-emerald-500 text-white'
                                : 'border-2 border-border bg-background dark:bg-card'"
                            class="flex size-7 shrink-0 items-center justify-center rounded-full transition-all"
                        >
                            <svg x-show="categoryType === 'priprava'" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                    </button>

                    <button
                        type="button"
                        @click="categoryType = 'ostalo'; $wire.set('categoryType', 'ostalo')"
                        :class="categoryType === 'ostalo'
                            ? 'border-emerald-400 bg-emerald-50 shadow-md shadow-emerald-100/50 dark:border-emerald-600 dark:bg-emerald-950/40 dark:shadow-emerald-950/40'
                            : 'border-border bg-background hover:border-emerald-200 hover:shadow-sm dark:hover:border-emerald-800'"
                        class="flex items-center justify-between rounded-xl border-2 p-4 text-left transition-all"
                    >
                        <div>
                            <span :class="categoryType === 'ostalo' ? 'text-emerald-700 dark:text-emerald-300' : 'text-foreground'" class="text-sm font-bold">Ostalo</span>
                            <span class="mt-0.5 block text-xs text-muted-foreground">Učni listi, preverjanje, predstavitve ...</span>
                        </div>
                        <div
                            :class="categoryType === 'ostalo'
                                ? 'bg-emerald-500 text-white'
                                : 'border-2 border-border bg-background dark:bg-card'"
                            class="flex size-7 shrink-0 items-center justify-center rounded-full transition-all"
                        >
                            <svg x-show="categoryType === 'ostalo'" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                    </button>
                </div>

                {{-- Ostalo subcategory picker --}}
                <div x-show="categoryType === 'ostalo'" x-cloak x-transition class="mt-5 space-y-2">
                    <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-emerald-600">
                        {{-- Tag icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                        </svg>
                        Kategorija gradiva
                    </label>
                    <select
                        wire:model="ostaloCategory"
                        class="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm text-foreground focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:focus:border-emerald-500 dark:focus:ring-emerald-900/70"
                    >
                        <option value="">Izberite kategorijo ...</option>
                        @foreach($ostaloCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <flux:error name="ostaloCategory" />
                </div>
            </div>

            {{-- ── Section 2: Razvrstitev ── --}}
            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/50">
                        {{-- School icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-teal-600 dark:text-teal-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">Razvrstitev</h2>
                </div>

                {{-- School type selector --}}
                <div class="space-y-2">
                    <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-teal-600">
                        {{-- School icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                        Šola
                    </label>
                    @php
                        $schoolTypeConfig = \App\Support\SchoolTypeUiConfig::all();
                    @endphp
                    <div class="grid gap-2 sm:grid-cols-3">
                        @foreach($schoolTypes as $type)
                            @php
                                $conf = $schoolTypeConfig[$type->slug] ?? $schoolTypeConfig['os'];
                                $colors = $conf['create'];
                                $iconSvg = $colors['iconSvg'];
                            @endphp
                            <button
                                type="button"
                                @click="selectSchoolType({{ $type->id }})"
                                :class="schoolTypeId == {{ $type->id }}
                                    ? '{{ $colors['active'] }}'
                                    : '{{ $colors['border'] }} {{ $colors['bg'] }} {{ $colors['text'] }} hover:shadow-sm'"
                                class="flex items-center justify-between rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-all"
                            >
                                <span class="flex items-center gap-2.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        {!! $iconSvg !!}
                                    </svg>
                                    <span class="capitalize">{{ $type->name }}</span>
                                </span>
                                <div
                                    :class="schoolTypeId == {{ $type->id }}
                                        ? '{{ $colors['checkBg'] }}'
                                        : '{{ $colors['checkBorder'] }}'"
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full transition-all"
                                >
                                    <svg x-show="schoolTypeId == {{ $type->id }}" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    <flux:error name="schoolTypeId" />
                </div>

                {{-- Grade + Subject side by side --}}
                <div x-show="schoolTypeId" x-cloak x-transition class="mt-5 grid gap-5 sm:grid-cols-2">
                    {{-- Grade --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-indigo-600">
                            {{-- GraduationCap icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>
                            Razred
                        </label>
                        <select
                            x-model="gradeId"
                            @change="$wire.set('gradeId', gradeId)"
                            class="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm text-foreground focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-900/70"
                        >
                            <option value="">Izberite razred ...</option>
                            <template x-for="grade in filteredGrades" :key="grade.id">
                                <option :value="grade.id" x-text="grade.name"></option>
                            </template>
                        </select>
                        <flux:error name="gradeId" />
                    </div>

                    {{-- Subject --}}
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-pink-600">
                            {{-- BookOpen icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                            Predmet
                        </label>

                        <flux:select wire:model="subjectId" variant="combobox" :filter="false" placeholder="Izberite predmet ...">
                            <x-slot name="input">
                                <flux:select.input wire:model.live.debounce.200ms="subjectSearch" placeholder="Izberite predmet ..." />
                            </x-slot>

                            @foreach($this->filteredSubjects as $subject)
                                <flux:select.option :value="$subject->id" wire:key="subject-{{ $subject->id }}">
                                    {{ $subject->name }}
                                </flux:select.option>
                            @endforeach

                            <flux:select.option.create wire:click="createSubject" min-length="2">
                                Dodaj "{{ $subjectSearch }}"
                            </flux:select.option.create>
                        </flux:select>

                        <flux:error name="subjectId" />
                    </div>
                </div>
            </div>

            {{-- ── Section 3: Podrobnosti ── --}}
            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/50">
                        {{-- AlignLeft icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-amber-600 dark:text-amber-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">Podrobnosti</h2>
                </div>

                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            {{-- Type icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                            </svg>
                            Naslov
                        </label>
                        <flux:input wire:model="title" placeholder="Npr. Matematika - ulomki" />
                        <flux:error name="title" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            {{-- Sparkles icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                            </svg>
                            Tema
                        </label>
                        <flux:input wire:model="topic" placeholder="Npr. Seštevanje in odštevanje ulomkov" />
                        <flux:error name="topic" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            {{-- Tag icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            Ključne besede
                        </label>
                        <flux:input wire:model="keywords" placeholder="Ločite z vejicami, npr. ulomki, matematika, 5. razred" />
                        <p class="text-xs text-muted-foreground">Ključne besede pomagajo pri iskanju vašega gradiva</p>
                        <flux:error name="keywords" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            {{-- AlignLeft icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                            </svg>
                            Opis
                        </label>
                        <flux:textarea wire:model="description" placeholder="Opišite vsebino gradiva ..." rows="4" />
                        <flux:error name="description" />
                    </div>
                </div>
            </div>

            {{-- ── Section 4: Datoteke ── --}}
            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/50">
                        {{-- Upload icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-sky-600 dark:text-sky-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">Datoteke</h2>
                </div>

                {{-- Drop zone --}}
                <flux:file-upload wire:model="files" multiple accept=".doc,.docx,.pdf,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <flux:file-upload.dropzone
                        heading="Povlecite datoteke sem ali kliknite za izbiro"
                        text="DOC, DOCX, PDF, PPT, PPTX, XLS, XLSX, JPG, PNG · Največ 20 MB na datoteko"
                        with-progress
                        class="!border-sky-200 !bg-sky-50/50 hover:!border-sky-300 hover:!bg-sky-50 in-data-dragging:!border-sky-400 in-data-dragging:!bg-sky-50 in-data-dragging:!shadow-inner dark:!border-sky-800 dark:!bg-sky-950/20 dark:hover:!border-sky-700 dark:hover:!bg-sky-950/35 dark:in-data-dragging:!border-sky-500 dark:in-data-dragging:!bg-sky-950/40"
                    />
                </flux:file-upload>

                <flux:error name="files" />
                <flux:error name="files.*" />

                {{-- File list --}}
                @if(count($files))
                    <div class="mt-4">
                        <p class="mb-2 text-xs font-semibold text-muted-foreground">
                            {{ count($files) }} {{ count($files) === 1 ? 'datoteka' : (count($files) === 2 ? 'datoteki' : (count($files) <= 4 ? 'datoteke' : 'datotek')) }}
                        </p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($files as $index => $file)
                                @php
                                    $ext = strtolower($file->getClientOriginalExtension());
                                    $icon = match(true) {
                                        $ext === 'pdf' => 'file-pdf',
                                        in_array($ext, ['doc', 'docx']) => 'file-word',
                                        in_array($ext, ['ppt', 'pptx']) => 'file-powerpoint',
                                        in_array($ext, ['xls', 'xlsx']) => 'file-excel',
                                        in_array($ext, ['jpg', 'jpeg', 'png']) => 'file-image',
                                        default => 'file-lines',
                                    };
                                    $fileColor = match(true) {
                                        in_array($ext, ['jpg', 'jpeg', 'png']) => 'bg-pink-100 dark:bg-pink-900/50 text-pink-600 dark:text-pink-400',
                                        in_array($ext, ['doc', 'docx']) => 'bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400',
                                        $ext === 'pdf' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400',
                                        in_array($ext, ['ppt', 'pptx']) => 'bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400',
                                        in_array($ext, ['xls', 'xlsx']) => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400',
                                        default => 'bg-muted text-muted-foreground',
                                    };
                                @endphp

                                <div class="group flex items-center gap-3 rounded-xl border border-border bg-background p-3 transition-all hover:shadow-sm">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $fileColor }}">
                                        <x-dynamic-component :component="'icon-regular.' . $icon" class="size-5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-foreground">{{ $file->getClientOriginalName() }}</p>
                                        <p class="text-xs text-muted-foreground">{{ \Illuminate\Support\Number::fileSize($file->getSize()) }}</p>
                                    </div>
                                    <span class="hidden rounded border border-border px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground sm:inline-flex">
                                        {{ $ext }}
                                    </span>
                                    <button type="button"
                                        wire:click="removeFile({{ $index }})"
                                        class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-950/50 dark:hover:text-rose-300"
                                        aria-label="Odstrani {{ $file->getClientOriginalName() }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── Submit ── --}}
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                <p class="text-xs text-muted-foreground">
                    Ko kliknete &ldquo;Dodaj gradivo&rdquo;, počakajte trenutek, da se datoteke naložijo.
                </p>
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-200/50 transition-colors hover:bg-emerald-700 dark:shadow-emerald-950/50 sm:w-auto"
                >
                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                        {{-- Upload icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        Dodaj gradivo
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="size-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Nalaganje ...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
