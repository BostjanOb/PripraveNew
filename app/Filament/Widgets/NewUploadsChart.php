<?php

namespace App\Filament\Widgets;

use App\Services\AdminDashboardMetricsService;
use Filament\Widgets\ChartWidget;

class NewUploadsChart extends ChartWidget
{
    protected ?string $heading = 'Nova gradiva';

    protected ?string $description = 'Dnevni trend v zadnjih 30 dneh';

    protected ?string $pollingInterval = null;

    protected string $color = 'primary';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $trend = app(AdminDashboardMetricsService::class)->getUploadsTrend();

        return [
            'datasets' => [
                [
                    'label' => 'Nova gradiva',
                    'data' => $trend['data'],
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
