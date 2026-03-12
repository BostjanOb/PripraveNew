<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsThirtyDayTrend;
use App\Services\AdminDashboardMetricsService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverviewStats extends StatsOverviewWidget
{
    use BuildsThirtyDayTrend;

    protected ?string $heading = 'Pregled';

    protected ?string $pollingInterval = null;

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $metrics = app(AdminDashboardMetricsService::class)->getOverview();

        return [
            Stat::make('Naložena gradiva', $this->formatNumber($metrics['uploads_total']))
                ->description('+'.$this->formatNumber($metrics['uploads_recent']).' v zadnjih 30 dneh')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->chart($metrics['uploads_chart'])
                ->color('primary'),
            Stat::make('Prenosi', $this->formatNumber($metrics['downloads_total']))
                ->description('+'.$this->formatNumber($metrics['downloads_recent']).' v zadnjih 30 dneh')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->chart($metrics['downloads_chart'])
                ->color('info'),
            Stat::make('Aktivni uporabniki', $this->formatNumber($metrics['active_users_total']))
                ->description($metrics['has_last_login_at']
                    ? '+'.$this->formatNumber($metrics['active_users_recent']).' v zadnjih 30 dneh'
                    : 'Manjka migracija last_login_at')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($metrics['active_users_chart'])
                ->color('success'),
        ];
    }
}
