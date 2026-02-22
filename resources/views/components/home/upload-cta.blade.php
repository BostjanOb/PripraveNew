@props(['userCount' => 0])

<section class="relative overflow-hidden px-4 py-16 md:py-20">
    {{-- Background --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 via-orange-50/80 to-rose-50/60 dark:from-amber-950/40 dark:via-orange-950/30 dark:to-rose-950/20"></div>
        <div class="absolute inset-0 opacity-[0.06] dark:opacity-[0.04]" style="background-image: radial-gradient(circle, #f59e0b 1px, transparent 1px); background-size: 24px 24px;"></div>
    </div>

    {{-- Decorations --}}
    <x-decorations.confetti-dots class="pointer-events-none absolute -right-6 -top-6 size-48 rotate-45 opacity-30" />
    <x-decorations.pencil class="pointer-events-none absolute bottom-8 left-8 hidden size-20 -rotate-12 opacity-20 lg:block" />

    <div class="relative mx-auto max-w-6xl">
        <div class="flex flex-col items-center gap-10 lg:flex-row lg:items-center lg:gap-16">
            {{-- Left: illustration area --}}
            <div class="hidden shrink-0 lg:flex lg:flex-col lg:items-center" aria-hidden="true">
                <div class="relative flex size-52 items-center justify-center">
                    {{-- Decorative rings --}}
                    <div class="absolute size-52 animate-[spin_30s_linear_infinite] rounded-full border-2 border-dashed border-amber-200/60"></div>
                    <div class="absolute size-40 animate-[spin_20s_linear_infinite_reverse] rounded-full border-2 border-dashed border-orange-200/50"></div>
                    {{-- Center icon --}}
                    <div class="relative flex size-24 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-orange-200/60">
                        {{-- Upload icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                    </div>
                    {{-- Floating badges --}}
                    <div class="absolute -right-2 top-4 flex size-10 items-center justify-center rounded-full bg-white shadow-md dark:bg-card">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-amber-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </div>
                    <div class="absolute -left-2 bottom-6 flex size-10 items-center justify-center rounded-full bg-white shadow-md dark:bg-card">
                        {{-- Heart icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-rose-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Right: content --}}
            <div class="flex-1 text-center lg:text-left">
                <div class="mb-4 inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-white/80 px-3 py-1 dark:border-orange-800 dark:bg-orange-950/50">
                    <x-icon-regular.sparkles class="size-3.5 text-orange-500" />
                    <span class="text-xs font-semibold text-orange-700">Ta teden dodanih 47 novih priprav</span>
                </div>

                <h2 class="font-serif text-2xl font-bold tracking-tight text-foreground md:text-3xl lg:text-4xl">
                    Imaš pripravo?
                    <span class="bg-linear-to-r from-amber-500 to-orange-600 bg-clip-text text-transparent">Deli jo s kolegi!</span>
                </h2>

                <p class="mt-3 text-pretty text-base text-muted-foreground md:text-lg">
                    Naloži učno pripravo, učni list ali drug učni material in pomagaj
                    učiteljem po vsej Sloveniji.
                </p>

                {{-- Benefits --}}
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4">
                    @php
                        $benefits = [
                            ['icon' => 'heart', 'text' => 'Pomagaj kolegom pri načrtovanju pouka', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50 dark:bg-rose-950/50'],
                            ['icon' => 'star', 'text' => 'Gradi svojo učiteljsko prepoznavnost', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-950/50'],
                            ['icon' => 'users', 'text' => 'Postani del skupnosti ' . number_format($userCount, 0, ',', '.') . '+ učiteljev', 'color' => 'text-teal-600', 'bg' => 'bg-teal-50 dark:bg-teal-950/50'],
                        ];
                    @endphp
                    @foreach ($benefits as $benefit)
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $benefit['bg'] }}">
                                @if ($benefit['icon'] === 'heart')
                                    <x-icon-regular.heart class="size-3.5 {{ $benefit['color'] }}" />
                                @elseif ($benefit['icon'] === 'star')
                                    <x-icon-regular.star class="size-3.5 {{ $benefit['color'] }}" />
                                    
                                @else
                                    <x-icon-regular.users class="size-3.5 {{ $benefit['color'] }}" />
                                @endif
                            </div>
                            <span class="text-sm font-medium text-foreground/80">{{ $benefit['text'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row lg:justify-start">
                    <a
                        href="{{ url('/dodajanje') }}"
                        class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-orange-200/50 transition-all hover:shadow-xl hover:shadow-orange-200/60 hover:brightness-105 dark:shadow-orange-900/30 dark:hover:shadow-orange-900/40"
                    >
                        <x-icon-regular.upload class="size-4" />
                        Dodaj pripravo
                        <x-icon-regular.arrow-right class="size-4 transition-transform group-hover:translate-x-0.5"/>
                    </a>
                    <span class="text-sm text-muted-foreground">
                        Hitro in enostavno &mdash; v manj kot 2 minutah.
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
