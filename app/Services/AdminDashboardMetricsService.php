<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DownloadRecord;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AdminDashboardMetricsService
{
    /**
     * @return array{uploads_total: int, uploads_recent: int, uploads_chart: array<int, int>, downloads_total: int, downloads_recent: int, downloads_chart: array<int, int>, active_users_total: int, active_users_recent: int, active_users_chart: array<int, int>, has_last_login_at: bool}
     */
    public function getOverview(): array
    {
        return $this->remember('overview', function (): array {
            $uploadsTrend = $this->getUploadsTrend();
            $downloadsTrend = $this->getDownloadsTrend();
            $activeUsersTrend = $this->getActiveUsersTrend();

            return [
                'uploads_total' => Document::whereNull('deleted_at')->count(),
                'uploads_recent' => array_sum($uploadsTrend['data']),
                'uploads_chart' => $uploadsTrend['data'],
                'downloads_total' => (int) Document::whereNull('deleted_at')->sum('downloads_count'),
                'downloads_recent' => array_sum($downloadsTrend['data']),
                'downloads_chart' => $downloadsTrend['data'],
                'active_users_total' => $activeUsersTrend['total'],
                'active_users_recent' => $activeUsersTrend['total'],
                'active_users_chart' => $activeUsersTrend['data'],
                'has_last_login_at' => $activeUsersTrend['has_last_login_at'],
            ];
        });
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getUploadsTrend(): array
    {
        return $this->remember('uploads-trend', function (): array {
            $rows = Document::whereNull('deleted_at')
                ->whereBetween('created_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
                ->groupByRaw('DATE(created_at)')
                ->orderByRaw('DATE(created_at)')
                ->get();

            return [
                'labels' => $this->getTrendLabels(),
                'data' => $this->mapTrendValues($rows),
            ];
        });
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getDownloadsTrend(): array
    {
        return $this->remember('downloads-trend', function (): array {
            $rows = DownloadRecord::whereBetween('created_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
                ->groupByRaw('DATE(created_at)')
                ->orderByRaw('DATE(created_at)')
                ->get();

            return [
                'labels' => $this->getTrendLabels(),
                'data' => $this->mapTrendValues($rows),
            ];
        });
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getRegistrationsTrend(): array
    {
        return $this->remember('registrations-trend', function (): array {
            $rows = User::where('role', 'user')
                ->whereBetween('created_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
                ->groupByRaw('DATE(created_at)')
                ->orderByRaw('DATE(created_at)')
                ->get();

            return [
                'labels' => $this->getTrendLabels(),
                'data' => $this->mapTrendValues($rows),
            ];
        });
    }

    /**
     * @return array{new_uploads: int, zero_download_uploads: int, average_downloads: float, top_subject: string, top_category: string}
     */
    public function getContentHealth(): array
    {
        return $this->remember('content-health', function (): array {
            $windowDocuments = Document::whereNull('deleted_at')
                ->whereBetween('created_at', [$this->getTrendStartDate(), $this->getTrendEndDate()]);

            $topSubject = Document::whereNull('documents.deleted_at')
                ->whereBetween('documents.created_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                ->join('subjects', 'subjects.id', '=', 'documents.subject_id')
                ->selectRaw('subjects.name as name, SUM(documents.downloads_count) as total_downloads')
                ->groupBy('subjects.id', 'subjects.name')
                ->orderByDesc('total_downloads')
                ->orderBy('subjects.name')
                ->first();

            $topCategory = Document::whereNull('documents.deleted_at')
                ->whereBetween('documents.created_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                ->join('categories', 'categories.id', '=', 'documents.category_id')
                ->selectRaw('categories.name as name, SUM(documents.downloads_count) as total_downloads')
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_downloads')
                ->orderBy('categories.name')
                ->first();

            return [
                'new_uploads' => (clone $windowDocuments)->count(),
                'zero_download_uploads' => (clone $windowDocuments)->where('downloads_count', 0)->count(),
                'average_downloads' => round((float) ((clone $windowDocuments)->avg('downloads_count') ?? 0), 1),
                'top_subject' => $topSubject?->name ?? 'Ni podatkov',
                'top_category' => $topCategory?->name ?? 'Ni podatkov',
            ];
        });
    }

    /**
     * @return array{total: int, data: array<int, int>, has_last_login_at: bool}
     */
    protected function getActiveUsersTrend(): array
    {
        return $this->remember('active-users-trend', function (): array {
            if (! Schema::hasColumn('users', 'last_login_at')) {
                return [
                    'total' => 0,
                    'data' => array_fill(0, 30, 0),
                    'has_last_login_at' => false,
                ];
            }

            $rows = User::where('role', 'user')
                ->whereNotNull('last_login_at')
                ->whereBetween('last_login_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                ->selectRaw('DATE(last_login_at) as date, COUNT(*) as aggregate')
                ->groupByRaw('DATE(last_login_at)')
                ->orderByRaw('DATE(last_login_at)')
                ->get();

            return [
                'total' => User::where('role', 'user')
                    ->whereNotNull('last_login_at')
                    ->whereBetween('last_login_at', [$this->getTrendStartDate(), $this->getTrendEndDate()])
                    ->count(),
                'data' => $this->mapTrendValues($rows),
                'has_last_login_at' => true,
            ];
        });
    }

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
     * @return array<int, string>
     */
    protected function getTrendLabels(): array
    {
        return $this->getTrendDays()
            ->map(fn (Carbon $date): string => $date->format('d.m.'))
            ->all();
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

    protected function remember(string $suffix, callable $callback): array
    {
        return Cache::remember(
            key: "admin-dashboard:{$suffix}",
            ttl: now()->addMinutes(2),
            callback: $callback,
        );
    }
}
