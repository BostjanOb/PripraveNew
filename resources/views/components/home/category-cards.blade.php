@props(['schoolTypes' => collect()])

@php
    $schoolTypeConfig = \App\Support\SchoolTypeUiConfig::all();
@endphp

<section class="mx-auto max-w-6xl px-4 py-8 md:py-10">
    <div class="mb-8 text-center">
        <div class="mb-3 inline-flex items-center gap-1.5 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 dark:border-teal-800 dark:bg-teal-950/50">
            <x-icon-regular.sparkles class="size-3.5 text-teal-500" />
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
                $typeConfig = $schoolTypeConfig[$schoolType->slug] ?? $schoolTypeConfig['os'];
                $config = $typeConfig['card'];
                $count = $schoolType->documents_count ?? 0;
            @endphp
            <a
                href="{{ route('browse', ['stopnja' => $schoolType->slug]) }}"
                class="group relative flex flex-col items-start gap-4 overflow-hidden rounded-2xl border-2 {{ $config['borderColor'] }} {{ $config['bgLight'] }} p-6 text-left transition-all {{ $config['hoverBorder'] }} hover:shadow-lg {{ $config['shadowColor'] }}"
            >
                {{-- Decorative corner gradient --}}
                <div class="absolute -right-8 -top-8 size-24 rounded-full bg-gradient-to-br {{ $config['gradient'] }} opacity-10 transition-all group-hover:scale-150 group-hover:opacity-20"></div>

                <div class="flex size-14 items-center justify-center rounded-2xl {{ $config['iconBg'] }}">
                    <x-dynamic-component :component="$typeConfig['icon']" class="size-7 {{ $config['iconColor'] }}" />
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
                        {{ \Illuminate\Support\Number::format($count) }} gradiv
                    </span>
                    <x-icon-regular.arrow-right class="size-4 {{ $config['iconColor'] }} opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100"/>
                </div>
            </a>
        @endforeach
    </div>
</section>
