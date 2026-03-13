<?php

namespace App\Enums;

enum BadgeCategory: string
{
    case Contribution = 'contribution';
    case Downloads = 'downloads';
    case Loyalty = 'loyalty';
    case Special = 'special';

    public function label(): string
    {
        return match ($this) {
            self::Contribution => 'Prispevki',
            self::Downloads => 'Prenosi',
            self::Loyalty => 'Zvestoba',
            self::Special => 'Posebne',
        };
    }

    /**
     * @return array{text: string, bg: string}
     */
    public function color(): array
    {
        return match ($this) {
            self::Contribution => ['text' => 'text-emerald-600 dark:text-emerald-300', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/40'],
            self::Downloads => ['text' => 'text-sky-600 dark:text-sky-300', 'bg' => 'bg-sky-50 dark:bg-sky-950/40'],
            self::Loyalty => ['text' => 'text-pink-600 dark:text-pink-300', 'bg' => 'bg-pink-50 dark:bg-pink-950/40'],
            self::Special => ['text' => 'text-fuchsia-600 dark:text-fuchsia-300', 'bg' => 'bg-fuchsia-50 dark:bg-fuchsia-950/40'],
        };
    }

    public function iconPath(): string
    {
        return match ($this) {
            self::Contribution => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />',
            self::Downloads => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />',
            self::Loyalty => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />',
            self::Special => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />',
        };
    }
}
