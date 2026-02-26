@props(['documentCount' => 0])

<section class="relative overflow-hidden px-4 pb-10 pt-10 md:pb-10.5 md:pt-10.5">
    {{-- Square grid notebook background --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 bg-background"></div>
        <x-icon-regular.sparkles class="absolute -top-4 left-1/2 size-16 -translate-x-1/2 opacity-20" />
        <div class="absolute inset-0 bg-linear-to-br from-teal-50/40 via-transparent to-amber-50/30 dark:from-teal-950/30 dark:via-transparent dark:to-amber-950/20"></div>
    </div>

    {{-- Floating confetti --}}
    <x-decorations.confetti-dots class="pointer-events-none absolute -left-8 -top-4 size-64 opacity-40" />
    <x-decorations.confetti-dots class="pointer-events-none absolute -bottom-8 right-0 size-52 rotate-180 opacity-30" />

    <div class="relative mx-auto flex max-w-6xl flex-col items-center gap-8 lg:flex-row lg:items-center lg:gap-4">
        {{-- Left: Text + Search --}}
        <div class="flex-1 text-center lg:text-left">
            <div class="mb-4 inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 dark:border-amber-800 dark:bg-amber-950/50">
                <x-icon-regular.sparkles class="size-3.5 text-amber-500" />
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                    Več kot {{ number_format($documentCount, 0, ',', '.') }} učnih priprav
                </span>
            </div>

            <h1 class="text-balance font-serif text-3xl font-bold tracking-tight text-foreground md:text-4xl lg:text-5xl">
                Priprave za pouk &mdash;
                <span class="bg-linear-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">hitro najdi</span> in prenesi.
            </h1>
            <p class="mt-4 text-pretty text-base text-muted-foreground md:text-lg">
                Učne priprave za predšolsko vzgojo, osnovno in srednjo šolo na enem mestu.
            </p>

            <form action="{{ route('browse') }}" method="GET" class="mt-8">
                <div class="relative mx-auto max-w-xl lg:mx-0">
                    <div class="flex items-center overflow-hidden rounded-2xl border-2 border-teal-200 bg-card shadow-lg shadow-teal-100/50 transition-colors focus-within:border-teal-400 focus-within:shadow-teal-200/50 dark:shadow-teal-900/30 dark:focus-within:shadow-teal-900/40">
                        <x-icon-regular.magnifying-glass class="ml-4 size-5 shrink-0 text-teal-500" />
                        <input
                            type="text"
                            name="q"
                            placeholder="Išči priprave, npr. matematika 2. razred..."
                            class="h-14 flex-1 bg-transparent px-3 text-base text-foreground placeholder:text-muted-foreground focus:outline-none"
                        />
                        <button
                            type="submit"
                            class="mr-2 rounded-xl bg-linear-to-r from-teal-500 to-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-teal-200/50 transition-all hover:shadow-lg hover:shadow-teal-200/60 dark:shadow-teal-900/30 dark:hover:shadow-teal-900/40"
                        >
                            Išči
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-5 flex flex-wrap items-center justify-center gap-2 lg:justify-start">
                <span class="text-sm text-muted-foreground">Popularno:</span>
                @php
                    $popularSearches = [
                        ['label' => 'Matematika 3. razred', 'color' => 'border-teal-200 bg-teal-50 text-teal-700 hover:bg-teal-100 dark:border-teal-800 dark:bg-teal-950/50 dark:text-teal-300 dark:hover:bg-teal-900/50'],
                        ['label' => 'Slovenščina', 'color' => 'border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100 dark:border-sky-800 dark:bg-sky-950/50 dark:text-sky-300 dark:hover:bg-sky-900/50'],
                        ['label' => 'Naravoslovje', 'color' => 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 dark:hover:bg-emerald-900/50'],
                        ['label' => 'Likovna vzgoja', 'color' => 'border-pink-200 bg-pink-50 text-pink-700 hover:bg-pink-100 dark:border-pink-800 dark:bg-pink-950/50 dark:text-pink-300 dark:hover:bg-pink-900/50'],
                        ['label' => 'Športna vzgoja', 'color' => 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-300 dark:hover:bg-amber-900/50'],
                    ];
                @endphp
                @foreach ($popularSearches as $term)
                    <a
                        href="{{ route('browse', ['q' => $term['label']]) }}"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition-colors {{ $term['color'] }}"
                    >
                        {{ $term['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Right: Books illustration --}}
        <div class="relative hidden shrink-0 lg:block" aria-hidden="true">
            <img
                src="{{ asset('images/books-stack.png') }}"
                alt=""
                width="380"
                height="300"
                class="w-[300px] drop-shadow-md xl:w-[360px]"
            />
        </div>
    </div>
</section>
