<?php

namespace App\Console\Commands;

use App\Models\DownloadDailyStat;
use App\Models\DownloadRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneDownloadRecordsCommand extends Command
{
    protected $signature = 'download-records:prune {--days=30 : Delete records older than this many days}';

    protected $description = 'Backfill daily aggregates and prune old download records';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days)->startOfDay();

        $this->info("Pruning download records older than {$cutoff->toDateString()} ({$days} days)...");

        // Backfill any missing daily stats for the period being pruned
        $missing = DB::table('download_records')
            ->where('created_at', '<', $cutoff)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('download_daily_stats')
                    ->whereColumn('download_daily_stats.date', DB::raw('DATE(download_records.created_at)'));
            })
            ->selectRaw('DATE(created_at) as date, COUNT(*) as download_count')
            ->groupByRaw('DATE(created_at)')
            ->get();

        if ($missing->isNotEmpty()) {
            DownloadDailyStat::upsert(
                $missing->map(fn ($row) => [
                    'date' => $row->date,
                    'download_count' => $row->download_count,
                ])->all(),
                ['date'],
                ['download_count'],
            );

            $this->info("Backfilled {$missing->count()} missing daily stat rows.");
        }

        // Delete old records in chunks to avoid locking the table for too long
        $totalDeleted = 0;

        do {
            $deleted = DownloadRecord::where('created_at', '<', $cutoff)
                ->limit(10000)
                ->delete();

            $totalDeleted += $deleted;

            if ($deleted > 0) {
                $this->output->write('.');
            }
        } while ($deleted > 0);

        if ($totalDeleted > 0) {
            $this->newLine();
        }

        $this->info("Pruned {$totalDeleted} download records.");

        return self::SUCCESS;
    }
}
