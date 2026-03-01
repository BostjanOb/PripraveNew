<div>
    <section class="relative border-y border-border bg-card">
        {{-- Playful decorations on the edges --}}
        <x-decorations.paperclip class="pointer-events-none absolute left-2 top-24 hidden w-5 -rotate-12 opacity-30 lg:block xl:left-8" />
        <x-decorations.small-plant class="pointer-events-none absolute -left-2 bottom-12 hidden w-12 opacity-25 lg:block xl:left-6" />
        <x-decorations.smiley-ball class="pointer-events-none absolute -right-1 bottom-24 hidden w-10 opacity-25 lg:block xl:right-6" />

        <div class="mx-auto max-w-6xl px-4 py-8 md:py-10">
            {{-- Header --}}
            <div class="mb-8 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <div class="mb-2 inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 dark:border-sky-800 dark:bg-sky-950/50">
                        <x-icon-regular.book-open class="size-3 text-sky-500" />
                        <span class="text-xs font-semibold text-sky-700 dark:text-sky-300">Najnovejše priprave</span>
                    </div>
                    <h2 class="font-serif text-2xl font-bold text-foreground md:text-3xl">
                        Zadnje dodane priprave
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Najnovejše učne priprave naših uporabnikov
                    </p>
                </div>
                <a href="{{ route('browse') }}" class="flex items-center gap-1 rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700 transition-colors hover:bg-teal-100 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-300 dark:hover:bg-teal-900/50">
                    Poglej vse
                    <x-icon-regular.angle-right class="size-4" />
                </a>
            </div>

            {{-- School type filter --}}
            <div class="mb-6">
                <div class="flex flex-wrap gap-2">
                    <button
                        wire:click="setActiveType('all')"
                        @class([
                            'rounded-full border px-4 py-2 text-sm font-semibold transition-all',
                            'border-foreground bg-foreground text-background shadow-md' => $activeType === 'all',
                            'border-border bg-card text-muted-foreground hover:border-foreground/30 hover:text-foreground' => $activeType !== 'all',
                        ])
                    >
                        Vse
                    </button>

                    @foreach ($schoolTypes as $st)
                        @php
                            $fs = $schoolTypeConfig[$st->slug] ?? $schoolTypeConfig['os'];
                        @endphp
                        <button
                            wire:click="setActiveType('{{ $st->slug }}')"
                            @class([
                                'flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition-all',
                                $fs['latestFilterActive'] => $activeType === $st->slug,
                                'border-border bg-card text-muted-foreground hover:border-foreground/30 hover:text-foreground' => $activeType !== $st->slug,
                            ])
                        >
                            <span @class([
                                'size-2.5 rounded-full',
                                $fs['dotActive'] => $activeType === $st->slug,
                                $fs['dot'] => $activeType !== $st->slug,
                            ])></span>
                            {{ $fs['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Results count --}}
            <div class="mb-4 flex items-center gap-2">
                <x-icon-regular.sparkles class="size-4 text-amber-500" />
                <span class="text-sm font-semibold text-foreground">{{ $documents->count() }} rezultatov</span>
            </div>

            {{-- Document list --}}
            <div class="space-y-2" wire:loading.class="opacity-50" wire:target="setActiveType">
                @forelse ($documents as $document)
                    <x-document-row :$document />
                @empty
                    <div class="rounded-2xl border-2 border-dashed border-muted bg-background py-16 text-center">
                        <x-icon-regular.file-lines  class="mx-auto size-12 text-muted-foreground/40" />
                        <p class="mt-3 text-sm text-muted-foreground">
                            Ni najdenih priprav za izbrane filtre.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Show more link --}}
            @if ($documents->isNotEmpty())
                <div class="mt-6 text-center">
                    <a
                        href="{{ $activeType !== 'all' ? route('browse', ['stopnja' => $activeType]) : route('browse') }}"
                        class="inline-block rounded-xl border-2 border-teal-200 bg-teal-50 px-6 py-2.5 text-sm font-semibold text-teal-700 transition-all hover:bg-teal-100 hover:shadow-md hover:shadow-teal-100/50 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-300 dark:hover:bg-teal-900/50"
                    >
                        Pokaži več priprav
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
