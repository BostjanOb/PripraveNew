@props(['schoolTypes' => collect()])

@php
    $categoryConfig = [
        'pv' => [
            'title' => 'Predšolska vzgoja',
            'description' => 'Priprave za vrtec in predšolsko obdobje',
            'icon' => 'baby',
            'gradient' => 'from-fuchsia-500 to-pink-500',
            'bgLight' => 'bg-fuchsia-50 dark:bg-fuchsia-950/30',
            'borderColor' => 'border-fuchsia-200 dark:border-fuchsia-800',
            'textColor' => 'text-fuchsia-700 dark:text-fuchsia-300',
            'iconBg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50',
            'iconColor' => 'text-fuchsia-600 dark:text-fuchsia-400',
            'badgeBg' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/50 dark:text-fuchsia-300',
            'hoverBorder' => 'hover:border-fuchsia-300 dark:hover:border-fuchsia-700',
            'shadowColor' => 'hover:shadow-fuchsia-100/50 dark:hover:shadow-fuchsia-900/30',
        ],
        'os' => [
            'title' => 'Osnovna šola',
            'description' => 'Priprave za 1. do 9. razred osnovne šole',
            'icon' => 'school',
            'gradient' => 'from-teal-500 to-emerald-500',
            'bgLight' => 'bg-teal-50 dark:bg-teal-950/30',
            'borderColor' => 'border-teal-200 dark:border-teal-800',
            'textColor' => 'text-teal-700 dark:text-teal-300',
            'iconBg' => 'bg-teal-100 dark:bg-teal-900/50',
            'iconColor' => 'text-teal-600 dark:text-teal-400',
            'badgeBg' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300',
            'hoverBorder' => 'hover:border-teal-300 dark:hover:border-teal-700',
            'shadowColor' => 'hover:shadow-teal-100/50 dark:hover:shadow-teal-900/30',
        ],
        'ss' => [
            'title' => 'Srednja šola',
            'description' => 'Priprave za gimnazije in srednje šole',
            'icon' => 'graduation-cap',
            'gradient' => 'from-orange-500 to-amber-500',
            'bgLight' => 'bg-orange-50 dark:bg-orange-950/30',
            'borderColor' => 'border-orange-200 dark:border-orange-800',
            'textColor' => 'text-orange-700 dark:text-orange-300',
            'iconBg' => 'bg-orange-100 dark:bg-orange-900/50',
            'iconColor' => 'text-orange-600 dark:text-orange-400',
            'badgeBg' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300',
            'hoverBorder' => 'hover:border-orange-300 dark:hover:border-orange-700',
            'shadowColor' => 'hover:shadow-orange-100/50 dark:hover:shadow-orange-900/30',
        ],
    ];
@endphp

<section class="mx-auto max-w-6xl px-4 py-8 md:py-10">
    <div class="mb-8 text-center">
        <div class="mb-3 inline-flex items-center gap-1.5 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 dark:border-teal-800 dark:bg-teal-950/50">
            {{-- Sparkles icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5 text-teal-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
            </svg>
            <span class="text-xs font-semibold text-teal-700 dark:text-teal-300">Izberite kategorijo</span>
        </div>
        <h2 class="font-serif text-2xl font-bold text-foreground md:text-3xl">
            Izberite stopnjo izobraževanja
        </h2>
        <p class="mt-2 text-sm text-muted-foreground">
            Brskajte po pripravah glede na stopnjo šole
        </p>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        @foreach ($schoolTypes as $schoolType)
            @php
                $config = $categoryConfig[$schoolType->slug] ?? $categoryConfig['os'];
                $count = $schoolType->documents_count ?? 0;
            @endphp
            <a
                href="{{ url('/brskanje?stopnja=' . $schoolType->slug) }}"
                class="group relative flex flex-col items-start gap-4 overflow-hidden rounded-2xl border-2 {{ $config['borderColor'] }} {{ $config['bgLight'] }} p-6 text-left transition-all {{ $config['hoverBorder'] }} hover:shadow-lg {{ $config['shadowColor'] }}"
            >
                {{-- Decorative corner gradient --}}
                <div class="absolute -right-8 -top-8 size-24 rounded-full bg-gradient-to-br {{ $config['gradient'] }} opacity-10 transition-all group-hover:scale-150 group-hover:opacity-20"></div>

                <div class="flex size-14 items-center justify-center rounded-2xl {{ $config['iconBg'] }}">
                    @if ($schoolType->slug === 'pv')
                        {{-- Baby icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 {{ $config['iconColor'] }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                        </svg>
                    @elseif ($schoolType->slug === 'os')
                        {{-- School icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 {{ $config['iconColor'] }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                        </svg>
                    @else
                        {{-- GraduationCap icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 {{ $config['iconColor'] }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-bold {{ $config['textColor'] }}">
                        {{ $config['title'] }}
                    </h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ $config['description'] }}
                    </p>
                </div>
                <div class="flex w-full items-center justify-between">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $config['badgeBg'] }}">
                        {{ number_format($count, 0, ',', '.') }} priprav
                    </span>
                    {{-- ArrowRight icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 {{ $config['iconColor'] }} opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
</section>
