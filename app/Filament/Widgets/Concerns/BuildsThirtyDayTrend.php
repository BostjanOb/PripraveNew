<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

trait BuildsThirtyDayTrend
{
    protected function getTrendStartDate(): Carbon
    {
        return now()->startOfDay()->subDays(29);
    }

    protected function getTrendEndDate(): Carbon
    {
        return now()->endOfDay();
    }

    /**
     * @return Collection<int, Carbon>
     */
    protected function getTrendDays(): Collection
    {
        $startDate = $this->getTrendStartDate();

        return collect(range(0, 29))
            ->map(fn (int $offset): Carbon => $startDate->copy()->addDays($offset));
    }

    /**
     * @param  Collection<int, object|array<string, mixed>>  $rows
     * @return array<int, int>
     */
    protected function mapTrendValues(Collection $rows, string $valueKey = 'aggregate', string $dateKey = 'date'): array
    {
        $valuesByDate = $rows->mapWithKeys(function (object|array $row) use ($dateKey, $valueKey): array {
            $date = is_array($row) ? $row[$dateKey] : $row->{$dateKey};
            $value = is_array($row) ? $row[$valueKey] : $row->{$valueKey};

            return [(string) $date => (int) round((float) $value)];
        });

        return $this->getTrendDays()
            ->map(fn (Carbon $date): int => (int) ($valuesByDate[$date->toDateString()] ?? 0))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function getTrendLabels(): array
    {
        return $this->getTrendDays()
            ->map(fn (Carbon $date): string => $date->format('d.m.'))
            ->all();
    }

    protected function formatNumber(int|float $value, int $decimals = 0): string
    {
        return Number::format($value, $decimals);
    }
}
