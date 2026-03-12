<?php

namespace App\Filament\Widgets;

use App\Services\AdminDashboardMetricsService;
use Filament\Widgets\ChartWidget;

class DownloadsChart extends ChartWidget
{
    protected ?string $heading = 'Prenosi';

    protected ?string $description = 'Dnevni trend v zadnjih 30 dneh';

    protected ?string $pollingInterval = null;

    protected string $color = 'info';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $trend = app(AdminDashboardMetricsService::class)->getDownloadsTrend();

        return [
            'datasets' => [
                [
                    'label' => 'Prenosi',
                    'data' => $trend['data'],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
