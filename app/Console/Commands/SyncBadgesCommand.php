<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class SyncBadgesCommand extends Command
{
    protected $signature = 'badges:sync {--all : Sync all users, not just recently active}';

    protected $description = 'Sync badges for users based on their activity';

    public function handle(BadgeService $badgeService): int
    {
        $query = User::query();

        if (! $this->option('all')) {
            $query->where('last_login_at', '>=', now()->subDays(30));
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No users to sync.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(100, function ($users) use ($badgeService, $bar): void {
            foreach ($users as $user) {
                $badgeService->syncBadges($user);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Synced badges for {$total} users.");

        return self::SUCCESS;
    }
}
