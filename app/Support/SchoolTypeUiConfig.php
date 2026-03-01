<?php

namespace App\Support;

class SchoolTypeUiConfig
{
    private const DEFAULT_SLUG = 'os';

    private const CONFIG = [
        'pv' => [
            'label' => 'Predšolska vzgoja',
            'shortLabel' => 'PV',
            'icon' => 'icon-regular.children',
            'badge' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 dark:bg-fuchsia-950/50 dark:text-fuchsia-300 dark:border-fuchsia-800',
            'filterActive' => 'border-fuchsia-400 bg-fuchsia-500 text-white',
            'latestFilterActive' => 'border-fuchsia-400 bg-fuchsia-500 text-white shadow-md shadow-fuchsia-200/50',
            'dot' => 'bg-fuchsia-500',
            'dotActive' => 'bg-white',
            'create' => [
                'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/40',
                'border' => 'border-fuchsia-200 dark:border-fuchsia-800',
                'text' => 'text-fuchsia-700 dark:text-fuchsia-300',
                'active' => 'border-fuchsia-400 bg-fuchsia-500 text-white shadow-md dark:border-fuchsia-500 dark:bg-fuchsia-600',
                'checkBg' => 'bg-white/30 text-white',
                'checkBorder' => 'border-2 border-current opacity-30',
                'iconSvg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />',
            ],
            'card' => [
                'title' => 'Predšolska vzgoja',
                'description' => 'Gradiva za vrtec in predšolsko obdobje',
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
            'level' => [
                'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/50',
                'text' => 'text-fuchsia-700 dark:text-fuchsia-300',
                'border' => 'border-fuchsia-200 dark:border-fuchsia-800',
                'dot' => 'bg-fuchsia-500',
                'icon' => 'bg-fuchsia-100 dark:bg-fuchsia-900/50 text-fuchsia-600 dark:text-fuchsia-400',
            ],
        ],
        'os' => [
            'label' => 'Osnovna šola',
            'shortLabel' => 'OS',
            'icon' => 'icon-regular.school',
            'badge' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
            'filterActive' => 'border-teal-400 bg-teal-500 text-white',
            'latestFilterActive' => 'border-teal-400 bg-teal-500 text-white shadow-md shadow-teal-200/50',
            'dot' => 'bg-teal-500',
            'dotActive' => 'bg-white',
            'create' => [
                'bg' => 'bg-teal-50 dark:bg-teal-950/40',
                'border' => 'border-teal-200 dark:border-teal-800',
                'text' => 'text-teal-700 dark:text-teal-300',
                'active' => 'border-teal-400 bg-teal-500 text-white shadow-md dark:border-teal-500 dark:bg-teal-600',
                'checkBg' => 'bg-white/30 text-white',
                'checkBorder' => 'border-2 border-current opacity-30',
                'iconSvg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />',
            ],
            'card' => [
                'title' => 'Osnovna šola',
                'description' => 'Gradiva za 1. do 9. razred osnovne šole',
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
            'level' => [
                'bg' => 'bg-teal-50 dark:bg-teal-950/50',
                'text' => 'text-teal-700 dark:text-teal-300',
                'border' => 'border-teal-200 dark:border-teal-800',
                'dot' => 'bg-teal-500',
                'icon' => 'bg-teal-100 dark:bg-teal-900/50 text-teal-600 dark:text-teal-400',
            ],
        ],
        'ss' => [
            'label' => 'Srednja šola',
            'shortLabel' => 'SŠ',
            'icon' => 'icon-regular.graduation-cap',
            'badge' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/50 dark:text-orange-300 dark:border-orange-800',
            'filterActive' => 'border-orange-400 bg-orange-500 text-white',
            'latestFilterActive' => 'border-orange-400 bg-orange-500 text-white shadow-md shadow-orange-200/50',
            'dot' => 'bg-orange-500',
            'dotActive' => 'bg-white',
            'create' => [
                'bg' => 'bg-orange-50 dark:bg-orange-950/40',
                'border' => 'border-orange-200 dark:border-orange-800',
                'text' => 'text-orange-700 dark:text-orange-300',
                'active' => 'border-orange-400 bg-orange-500 text-white shadow-md dark:border-orange-500 dark:bg-orange-600',
                'checkBg' => 'bg-white/30 text-white',
                'checkBorder' => 'border-2 border-current opacity-30',
                'iconSvg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />',
            ],
            'card' => [
                'title' => 'Srednja šola',
                'description' => 'Gradiva za gimnazije in srednje šole',
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
            'level' => [
                'bg' => 'bg-orange-50 dark:bg-orange-950/50',
                'text' => 'text-orange-700 dark:text-orange-300',
                'border' => 'border-orange-200 dark:border-orange-800',
                'dot' => 'bg-orange-500',
                'icon' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400',
            ],
        ],
    ];

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return self::CONFIG;
    }

    /**
     * @return array<string, mixed>
     */
    public static function forSlug(?string $slug): array
    {
        $normalizedSlug = strtolower(trim((string) $slug));

        return self::CONFIG[$normalizedSlug] ?? self::CONFIG[self::DEFAULT_SLUG];
    }
}
