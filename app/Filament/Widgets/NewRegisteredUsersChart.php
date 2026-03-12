<?php

namespace App\Filament\Widgets;

use App\Services\AdminDashboardMetricsService;
use Filament\Widgets\ChartWidget;

class NewRegisteredUsersChart extends ChartWidget
{
    protected ?string $heading = 'Novi uporabniki';

    protected ?string $description = 'Dnevni trend v zadnjih 30 dneh';

    protected ?string $pollingInterval = null;

    protected string $color = 'success';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $trend = app(AdminDashboardMetricsService::class)->getRegistrationsTrend();

        return [
            'datasets' => [
                [
                    'label' => 'Novi uporabniki',
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
