@props(['progress'])
{{--
  $progress is the array returned by BadgeService::getContributionProgress(User):
  [
    'uploadCount'             => int,
    'previousMilestone'       => int,
    'nextMilestone'           => int|null,
    'nextBadge'               => array|null,
    'progressPercent'         => float,
    'uploadsToNext'           => int,
  ]
--}}

@php
    $uploadCount       = $progress['uploadCount'];
    $previousMilestone = $progress['previousMilestone'];
    $nextMilestone     = $progress['nextMilestone'];
    $nextBadge         = $progress['nextBadge'];
    $progressPercent   = $progress['progressPercent'];
    $uploadsToNext     = $progress['uploadsToNext'];

    $pripravaWord = function (int $n): string {
        if ($n === 1) { return 'priprava'; }
        if ($n === 2) { return 'pripravi'; }
        if ($n <= 4)  { return 'priprave'; }
        return 'priprav';
    };
@endphp

<div class="mb-6 rounded-2xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 p-4 md:p-5 dark:border-emerald-800/70 dark:from-emerald-950/60 dark:via-teal-950/50 dark:to-cyan-950/50">
    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div>
            <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300">
                <div class="flex size-8 items-center justify-center rounded-lg bg-white/80 dark:bg-emerald-900/60">
                    {{-- Target / crosshair icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v1.5m0 12v1.5m7.5-7.5h-1.5m-12 0H4.5m13.773-5.773-1.061 1.06m-9.424 9.425-1.06 1.06m12.545.001-1.061-1.061m-9.424-9.424-1.06-1.06M12 9.75a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold">Moj vpliv</p>
            </div>

            <p class="mt-2 text-sm text-emerald-800 dark:text-emerald-200">
                @if($nextBadge)
                    Še {{ $uploadsToNext }} {{ $pripravaWord($uploadsToNext) }} do značke "{{ $nextBadge['name'] }}"!
                @else
                    Dosegli ste najvišjo prispevno značko. Hvala za vaš izjemen doprinos skupnosti!
                @endif
            </p>
        </div>

        <div class="shrink-0 rounded-xl border border-emerald-200 bg-white/70 px-3 py-2 text-right dark:border-emerald-800 dark:bg-emerald-950/50">
            <p class="text-xs text-emerald-700/80 dark:text-emerald-300/80">Naložene priprave</p>
            <p class="text-base font-bold text-emerald-800 dark:text-emerald-200">
                {{ $uploadCount }}{{ $nextMilestone ? ' / ' . $nextMilestone : '' }}
            </p>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="mt-4 h-3 w-full overflow-hidden rounded-full bg-white/80 dark:bg-emerald-900/50">
        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500"
             style="width: {{ number_format($progressPercent, 1) }}%"></div>
    </div>

    <div class="mt-2 flex items-center justify-between text-xs text-emerald-700/90 dark:text-emerald-300/90">
        <span>{{ $previousMilestone }} osnova</span>
        <span>
            @if($nextMilestone)
                {{ $nextMilestone }} za naslednjo značko
            @else
                Vse prispevne značke osvojene
            @endif
        </span>
    </div>
</div>
