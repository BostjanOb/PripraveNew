<?php

namespace App\Support;

class CategoryTypeUiConfig
{
    private const DEFAULT_SLUG = 'priprava';

    /** @var array<string, array{badge: string, abbr: string}> */
    private const CONFIG = [
        'priprava' => ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800', 'abbr' => 'P'],
        'delovni-list' => ['badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800', 'abbr' => 'DL'],
        'test' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'T'],
        'preverjanje-znanja' => ['badge' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800', 'abbr' => 'PZ'],
        'ucni-list' => ['badge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800', 'abbr' => 'UL'],
        'ostalo' => ['badge' => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-950/50 dark:text-gray-300 dark:border-gray-800', 'abbr' => 'O'],
    ];

    /**
     * @return array<string, array{badge: string, abbr: string}>
     */
    public static function all(): array
    {
        return self::CONFIG;
    }

    /**
     * @return array{badge: string, abbr: string}
     */
    public static function forSlug(?string $slug): array
    {
        return self::CONFIG[$slug ?? ''] ?? self::CONFIG[self::DEFAULT_SLUG];
    }
}
