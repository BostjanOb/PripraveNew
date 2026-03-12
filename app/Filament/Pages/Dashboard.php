<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminOverviewStats;
use App\Filament\Widgets\DownloadsChart;
use App\Filament\Widgets\LatestDocumentsTable;
use App\Filament\Widgets\NewRegisteredUsersChart;
use App\Filament\Widgets\NewUploadsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Nadzorna plošča';

    public function getWidgets(): array
    {
        return [
            AdminOverviewStats::class,
            NewUploadsChart::class,
            DownloadsChart::class,
            NewRegisteredUsersChart::class,
            LatestDocumentsTable::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}
