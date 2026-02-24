<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /** @use HasFactory<\Database\Factories\FaqFactory> */
    use HasFactory;

    /**
     * @return array<string, array{label: string, icon_color: string, background_class: string, icon_class: string}>
     */
    public static function iconColorPalette(): array
    {
        return [
            '#CCFBF1' => ['label' => 'Turkizna', 'icon_color' => '#0D9488', 'background_class' => 'bg-teal-100', 'icon_class' => 'text-teal-600'],
            '#FEF3C7' => ['label' => 'Jantarna', 'icon_color' => '#D97706', 'background_class' => 'bg-amber-100', 'icon_class' => 'text-amber-600'],
            '#D1FAE5' => ['label' => 'Smaragdna', 'icon_color' => '#059669', 'background_class' => 'bg-emerald-100', 'icon_class' => 'text-emerald-600'],
            '#E0F2FE' => ['label' => 'Nebesno modra', 'icon_color' => '#0284C7', 'background_class' => 'bg-sky-100', 'icon_class' => 'text-sky-600'],
            '#EDE9FE' => ['label' => 'Vijolicna', 'icon_color' => '#7C3AED', 'background_class' => 'bg-violet-100', 'icon_class' => 'text-violet-600'],
            '#FFE4E6' => ['label' => 'Roza rdeca', 'icon_color' => '#E11D48', 'background_class' => 'bg-rose-100', 'icon_class' => 'text-rose-600'],
            '#FCE7F3' => ['label' => 'Roza', 'icon_color' => '#DB2777', 'background_class' => 'bg-pink-100', 'icon_class' => 'text-pink-600'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function iconBackgroundColorOptions(): array
    {
        return collect(static::iconColorPalette())
            ->mapWithKeys(fn (array $color, string $hex): array => [$hex => "{$color['label']} ({$hex})"])
            ->all();
    }

    public function iconBackgroundClass(): string
    {
        return static::iconColorPalette()[$this->icon_background_color]['background_class'] ?? 'bg-slate-100';
    }

    public function iconForegroundClass(): string
    {
        return static::iconColorPalette()[$this->icon_background_color]['icon_class'] ?? 'text-slate-600';
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order');
    }
}
