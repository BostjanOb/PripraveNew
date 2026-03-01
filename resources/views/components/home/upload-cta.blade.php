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
                        <x-icon-regular.upload  class="size-12 text-white"/>
                    </div>
                    {{-- Floating badges --}}
                    <div class="absolute -right-2 top-4 flex size-10 items-center justify-center rounded-full bg-white shadow-md dark:bg-card">
                        <x-icon-regular.sparkles class="size-5 text-amber-500"/>
                    </div>
                    <div class="absolute -left-2 bottom-6 flex size-10 items-center justify-center rounded-full bg-white shadow-md dark:bg-card">
                        <x-icon-regular.heart class="size-5 text-rose-400"/>
                    </div>
                </div>
            </div>

            {{-- Right: content --}}
            <div class="flex-1 text-center lg:text-left">
                <div class="mb-4 inline-flex items-center gap-1.5 rounded-full border border-orange-200 bg-white/80 px-3 py-1 dark:border-orange-800 dark:bg-orange-950/50">
                    <x-icon-regular.sparkles class="size-3.5 text-orange-500" />
                    <span class="text-xs font-semibold text-orange-700">Ta teden dodanih 47 novih gradiv</span>
                </div>

                <h2 class="font-serif text-2xl font-bold tracking-tight text-foreground md:text-3xl lg:text-4xl">
                    Imaš gradivo?
                    <span class="bg-linear-to-r from-amber-500 to-orange-600 bg-clip-text text-transparent">Deli ga s kolegi!</span>
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
