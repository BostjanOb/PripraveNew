@props([
    'documentCount' => 0,
    'userCount' => 0,
    'downloadCount' => 0,
    'averageRating' => '0',
])
@php
    $stats = [
        [
            'label' => 'Učnih priprav',
            'value' => number_format($documentCount, 0, ',', '.'),
            'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/50',
            'iconColor' => 'text-emerald-600 dark:text-emerald-400',
            'borderColor' => 'border-emerald-200 dark:border-emerald-800',
            'bgColor' => 'bg-emerald-50/50 dark:bg-emerald-950/30',
            'valueColor' => 'text-emerald-700 dark:text-emerald-400',
            'icon' => 'icon-regular.files',
        ],
        [
            'label' => 'Registriranih učiteljev',
            'value' => number_format($userCount, 0, ',', '.'),
            'iconBg' => 'bg-sky-100 dark:bg-sky-900/50',
            'iconColor' => 'text-sky-600 dark:text-sky-400',
            'borderColor' => 'border-sky-200 dark:border-sky-800',
            'bgColor' => 'bg-sky-50/50 dark:bg-sky-950/30',
            'valueColor' => 'text-sky-700 dark:text-sky-400',
            'icon' => 'icon-regular.users',
        ],
        [
            'label' => 'Prenosov',
            'value' => number_format($downloadCount, 0, ',', '.') . '+',
            'iconBg' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50',
            'iconColor' => 'text-fuchsia-600 dark:text-fuchsia-400',
            'borderColor' => 'border-fuchsia-200 dark:border-fuchsia-800',
            'bgColor' => 'bg-fuchsia-50/50 dark:bg-fuchsia-950/30',
            'valueColor' => 'text-fuchsia-700 dark:text-fuchsia-400',
            'icon' => 'icon-regular.download',
        ],
        [
            'label' => 'Povprečna ocena',
            'value' => $averageRating,
            'iconBg' => 'bg-amber-100 dark:bg-amber-900/50',
            'iconColor' => 'text-amber-600 dark:text-amber-400',
            'borderColor' => 'border-amber-200 dark:border-amber-800',
            'bgColor' => 'bg-amber-50/50 dark:bg-amber-950/30',
            'valueColor' => 'text-amber-700 dark:text-amber-400',
            'icon' => 'icon-regular.star',
        ],
    ];
@endphp

<section class="mx-auto max-w-6xl px-4 py-8 md:py-10">
    <div class="mb-8 text-center">
        <div class="mb-3 inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 dark:border-amber-800 dark:bg-amber-950/50">
            <x-icon-regular.sparkles class="size-3.5 text-amber-500" />
            <span class="text-xs font-semibold text-amber-700 dark:text-amber-300">Priprave.net v številkah</span>
        </div>
        <h2 class="font-serif text-2xl font-bold text-foreground md:text-3xl">
            Skupnost, ki raste
        </h2>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="group flex flex-col items-center gap-3 rounded-2xl border-2 {{ $stat['borderColor'] }} {{ $stat['bgColor'] }} p-6 text-center transition-all hover:shadow-lg">
                <div class="flex size-12 items-center justify-center rounded-2xl {{ $stat['iconBg'] }} transition-transform group-hover:scale-110">
                    <x-dynamic-component :component="$stat['icon']" class="size-5 {{ $stat['iconColor'] }}" />
                </div>
                <span class="text-2xl font-bold {{ $stat['valueColor'] }} md:text-3xl">
                    {{ $stat['value'] }}
                </span>
                <span class="text-xs font-medium text-muted-foreground">{{ $stat['label'] }}</span>
            </div>
        @endforeach
    </div>
</section>
