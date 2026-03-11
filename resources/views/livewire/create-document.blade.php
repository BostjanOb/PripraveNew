<div class="relative">
    @php
        $isEditing = $this->isEditing();
    @endphp

    @if($submitted)
        <div class="mx-auto max-w-2xl px-4 py-20 text-center">
            <div class="mx-auto mb-6 flex size-20 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/40">
                <x-icon-regular.check-circle class="size-10 text-emerald-600 dark:text-emerald-300"/>
            </div>
            <h1 class="font-serif text-3xl font-bold text-foreground">{{ $isEditing ? 'Gradivo uspešno posodobljeno!' : 'Gradivo uspešno dodano!' }}</h1>
            <p class="mt-3 text-base text-muted-foreground">
                {{ $isEditing ? 'Spremembe so bile uspešno shranjene.' : 'Vaše gradivo je bilo uspešno naloženo in bo kmalu dostopno drugim uporabnikom.' }}
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <flux:button wire:click="resetForm" variant="outline" class="gap-1.5" icon="icon-regular.upload">
                    {{ $isEditing ? 'Nadaljuj z urejanjem' : 'Dodaj novo gradivo' }}
                </flux:button>
                <flux:button as="a" href="{{ $isEditing ? route('document.show', ['document' => $editingDocumentSlug]) : route('home') }}"
                             :icon="$isEditing ? 'icon-regular.eye' : 'icon-regular.book-open'"
                             class="gap-1.5 bg-teal-500 text-white hover:bg-teal-600">
                    {{ $isEditing ? 'Poglej gradivo' : 'Na brskanje' }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="relative overflow-hidden border-b border-border bg-card">
            <x-decorations.color-dots class="absolute -left-6 -top-2 size-48 opacity-30" />
            <x-decorations.color-dots class="absolute -right-4 bottom-0 size-36 rotate-180 opacity-20" />

            <div class="relative mx-auto max-w-4xl px-4 py-10 text-center md:py-12">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/50">
                    <x-icon-regular.upload class="size-9 text-emerald-600 dark:text-emerald-300" />
                </div>
                <h1 class="font-serif text-3xl font-bold text-foreground md:text-4xl">{{ $isEditing ? 'Urejanje gradiva' : 'Dodajanje gradiva' }}</h1>
                <p class="mt-3 text-base text-muted-foreground md:text-lg">
                    {{ $isEditing ? 'Posodobite podatke gradiva in po potrebi zamenjajte datoteke.' : 'Čim natančneje izpolnite spodnja polja in s tem omogočite lažje iskanje gradiv.' }}
                </p>
            </div>
        </div>

        <form
            wire:submit="submit"
            x-data="{
                categoryType: $wire.entangle('form.categoryType'),
                schoolTypeId: $wire.entangle('form.schoolTypeId'),
                gradeId: $wire.entangle('form.gradeId'),
                allGrades: @js($grades->map(fn($g) => ['id' => $g->id, 'name' => $g->name, 'school_type_id' => $g->school_type_id])),
                get filteredGrades() {
                    return this.allGrades.filter(g => g.school_type_id == this.schoolTypeId);
                },
                selectSchoolType(id) {
                    this.schoolTypeId = String(id);
                    this.gradeId = '';
                    $wire.set('form.subjectId', '');
                    $wire.set('form.subjectSearch', '');
                },
                selectGrade(id) {
                    this.gradeId = String(id);
                },
            }"
            class="mx-auto max-w-4xl space-y-8 px-4 py-8 md:py-12"
        >
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

                <div class="grid gap-3 sm:grid-cols-2">
                    <button
                        type="button"
                        @click="categoryType = 'priprava'; $wire.set('form.ostaloCategory', '')"
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
                            <x-icon-regular.check class="size-4" x-show="categoryType === 'priprava'" x-cloak />
                        </div>
                    </button>

                    <button
                        type="button"
                        @click="categoryType = 'ostalo'"
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
                            <x-icon-regular.check class="size-4" x-show="categoryType === 'ostalo'" x-cloak />
                        </div>
                    </button>
                </div>

                <div x-show="categoryType === 'ostalo'" x-cloak x-transition class="mt-5 space-y-2">
                    <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-emerald-600">
                        <x-icon-regular.tag class="size-3.5" />
                        Kategorija gradiva
                    </label>
                    <select
                        wire:model="form.ostaloCategory"
                        class="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm text-foreground focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:focus:border-emerald-500 dark:focus:ring-emerald-900/70"
                    >
                        <option value="">Izberite kategorijo ...</option>
                        @foreach($ostaloCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <flux:error name="form.ostaloCategory" />
                </div>
            </div>

            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/50">
                        <x-icon-regular.school class="size-4 text-teal-600 dark:text-teal-300" />
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">Razvrstitev</h2>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-teal-600">
                        <x-icon-regular.graduation-cap class="size-3.5" />
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
                            @endphp
                            <button
                                wire:key="school-type-{{ $type->id }}"
                                type="button"
                                @click="selectSchoolType({{ $type->id }})"
                                :class="schoolTypeId == {{ $type->id }}
                                    ? '{{ $colors['active'] }}'
                                    : '{{ $colors['border'] }} {{ $colors['bg'] }} {{ $colors['text'] }} hover:shadow-sm'"
                                class="flex items-center justify-between rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-all"
                            >
                                <span class="flex items-center gap-2.5">
                                    <x-dynamic-component :component="$conf['icon']" class="size-4" />
                                    <span class="capitalize">{{ $type->name }}</span>
                                </span>
                                <div
                                    :class="schoolTypeId == {{ $type->id }}
                                        ? '{{ $colors['checkBg'] }}'
                                        : '{{ $colors['checkBorder'] }}'"
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full transition-all"
                                >
                                    <x-icon-regular.check class="size-3" x-show="schoolTypeId == {{ $type->id }}" x-cloak />
                                </div>
                            </button>
                        @endforeach
                    </div>
                    <flux:error name="form.schoolTypeId" />
                </div>

                <div x-show="schoolTypeId" x-cloak x-transition class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-indigo-600">
                            <x-icon-regular.graduation-cap class="size-3.5"/>
                            Razred
                        </label>
                        <select
                            x-model="gradeId"
                            class="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm text-foreground focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:border-indigo-500 dark:focus:ring-indigo-900/70"
                        >
                            <option value="">Izberite razred ...</option>
                            <template x-for="grade in filteredGrades" :key="grade.id">
                                <option :value="grade.id" x-text="grade.name"></option>
                            </template>
                        </select>
                        <flux:error name="form.gradeId" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-pink-600">
                            <x-icon-regular.book-open class="size-3.5"/>
                            Predmet
                        </label>

                        <flux:select wire:model="form.subjectId" variant="combobox" :filter="false" placeholder="Izberite predmet ...">
                            <x-slot name="input">
                                <flux:select.input wire:model.live.debounce.200ms="form.subjectSearch" placeholder="Izberite predmet ..." />
                            </x-slot>

                            @foreach($this->filteredSubjects as $subject)
                                <flux:select.option :value="$subject->id" wire:key="subject-{{ $subject->id }}">
                                    {{ $subject->name }}
                                </flux:select.option>
                            @endforeach

                            <flux:select.option.create wire:click="createSubject" min-length="2">
                                Dodaj "{{ $form->subjectSearch }}"
                            </flux:select.option.create>
                        </flux:select>

                        <flux:error name="form.subjectId" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/50">
                        <x-icon-regular.align-left class="size-4 text-amber-600 dark:text-amber-300"/>
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">Podrobnosti</h2>
                </div>

                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            <x-icon-regular.comment-alt-captions class="size-3.5"/>
                            Naslov
                        </label>
                        <flux:input wire:model="form.title" placeholder="Npr. Matematika - ulomki" />
                        <flux:error name="form.title" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            <x-icon-regular.sparkles class="size-3.5"/>
                            Tema
                        </label>
                        <flux:input wire:model="form.topic" placeholder="Npr. Seštevanje in odštevanje ulomkov" />
                        <flux:error name="form.topic" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            <x-icon-regular.tag class="size-3.5"/>
                            Ključne besede
                        </label>
                        <flux:input wire:model="form.keywords" placeholder="Ločite z vejicami, npr. ulomki, matematika, 5. razred" />
                        <p class="text-xs text-muted-foreground">Ključne besede pomagajo pri iskanju vašega gradiva</p>
                        <flux:error name="form.keywords" />
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-600">
                            <x-icon-regular.align-justify class="size-3.5"/>
                            Opis
                        </label>
                        <flux:textarea wire:model="form.description" placeholder="Opišite vsebino gradiva ..." rows="4" />
                        <flux:error name="form.description" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
                <div class="mb-5 flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/50">
                        <x-icon-regular.upload class="size-4 text-sky-600 dark:text-sky-300"/>
                    </div>
                    <h2 class="font-serif text-lg font-bold text-foreground">{{ $isEditing ? 'Datoteke gradiva' : 'Datoteke' }}</h2>
                </div>

                @if($isEditing)
                    <p class="mb-4 text-sm text-muted-foreground">
                        Obstoječe datoteke lahko odstranite posamično. Nove naložene datoteke se dodajo k obstoječim.
                    </p>
                @endif

                <flux:file-upload wire:model="form.files" multiple accept=".doc,.docx,.pdf,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <flux:file-upload.dropzone
                        :heading="$isEditing ? 'Povlecite nove datoteke sem ali kliknite za dodajanje' : 'Povlecite datoteke sem ali kliknite za izbiro'"
                        text="DOC, DOCX, PDF, PPT, PPTX, XLS, XLSX, JPG, PNG · Največ 20 MB na datoteko"
                        with-progress
                        class="!border-sky-200 !bg-sky-50/50 hover:!border-sky-300 hover:!bg-sky-50 in-data-dragging:!border-sky-400 in-data-dragging:!bg-sky-50 in-data-dragging:!shadow-inner dark:!border-sky-800 dark:!bg-sky-950/20 dark:hover:!border-sky-700 dark:hover:!bg-sky-950/35 dark:in-data-dragging:!border-sky-500 dark:in-data-dragging:!bg-sky-950/40"
                    />
                </flux:file-upload>

                <flux:error name="form.files" />
                <flux:error name="form.files.*" />

                @if($isEditing && count($form->existingFiles))
                    <div class="mt-4">
                        <p class="mb-2 text-xs font-semibold text-muted-foreground">
                            Trenutno shranjene datoteke
                        </p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($form->existingFiles as $file)
                                @php
                                    $ext = strtolower($file['extension']);
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

                                <div class="flex items-center gap-3 rounded-xl border border-border bg-background p-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $fileColor }}">
                                        <x-dynamic-component :component="'icon-regular.' . $icon" class="size-5" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-foreground">{{ $file['name'] }}</p>
                                        <p class="text-xs text-muted-foreground">{{ \Illuminate\Support\Number::fileSize($file['size']) }}</p>
                                    </div>
                                    <span class="hidden rounded border border-border px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground sm:inline-flex">
                                        {{ $ext }}
                                    </span>
                                    <button type="button"
                                        wire:click="removeExistingFile({{ $file['id'] }})"
                                        class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-950/50 dark:hover:text-rose-300"
                                        aria-label="Odstrani {{ $file['name'] }}">
                                        <x-icon-regular.x class="size-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($form->files))
                    <div class="mt-4">
                        <p class="mb-2 text-xs font-semibold text-muted-foreground">
                            {{ $isEditing ? 'Nove datoteke' : count($form->files) . ' ' . match(true) { count($form->files) === 1 => 'datoteka', count($form->files) === 2 => 'datoteki', count($form->files) <= 4 => 'datoteke', default => 'datotek' } }}
                        </p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($form->files as $index => $file)
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

                                <div wire:key="file-{{ $index }}" class="group flex items-center gap-3 rounded-xl border border-border bg-background p-3 transition-all hover:shadow-sm">
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
                                        <x-icon-regular.x class="size-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                <p class="text-xs text-muted-foreground">
                    {{ $isEditing ? 'Ko kliknete "Shrani spremembe", počakajte trenutek, če ste naložili nove datoteke.' : 'Ko kliknete "Dodaj gradivo", počakajte trenutek, da se datoteke naložijo.' }}
                </p>
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-8 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-200/50 transition-colors hover:bg-emerald-700 dark:shadow-emerald-950/50 sm:w-auto"
                >
                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                        <x-icon-regular.upload class="size-5" />
                        {{ $isEditing ? 'Shrani spremembe' : 'Dodaj gradivo' }}
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <x-icon-regular.loader class="size-5 animate-spin" />
                        {{ $isEditing ? 'Shranjevanje ...' : 'Nalaganje ...' }}
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
