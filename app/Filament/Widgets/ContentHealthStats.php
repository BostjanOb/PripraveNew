<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsThirtyDayTrend;
use App\Services\AdminDashboardMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentHealthStats extends StatsOverviewWidget
{
    use BuildsThirtyDayTrend;

    protected ?string $heading = 'Zdravje vsebin';

    protected ?string $description = 'Kakovost novih gradiv v zadnjih 30 dneh';

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $metrics = app(AdminDashboardMetricsService::class)->getContentHealth();

        return [
            Stat::make('Nova gradiva', $this->formatNumber($metrics['new_uploads']))
                ->description('V zadnjih 30 dneh')
                ->descriptionIcon('heroicon-m-document-plus')
                ->color('primary'),
            Stat::make('Brez prenosov', $this->formatNumber($metrics['zero_download_uploads']))
                ->description($metrics['new_uploads'] > 0
                    ? $this->formatNumber(($metrics['zero_download_uploads'] / $metrics['new_uploads']) * 100, 1).'% novih gradiv'
                    : 'Ni novih gradiv')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('warning'),
            Stat::make('Povp. prenosi / gradivo', $this->formatNumber($metrics['average_downloads'], 1))
                ->description('Za nova gradiva')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
            Stat::make('Top predmet', $metrics['top_subject'])
                ->description('Kategorija: '.$metrics['top_category'])
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),
        ];
    }
}
