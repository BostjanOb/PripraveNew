<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\progress;

class SyncBadgesCommand extends Command
{
    protected $signature = 'badges:sync {--all : Sync all users, not just recently active}';

    protected $description = 'Sync badges for users based on their activity';

    public function handle(BadgeService $badgeService): int
    {
        ini_set('memory_limit', '1G');
        progress(
            label: 'Syncing badges...',
            steps: User::with('badges')
                ->withCount([
                    'downloadRecords',
                    'documents',
                    'comments',
                    'documents as distinct_subject_count' => fn ($q) => $q->select(DB::raw('count(distinct(subject_id))')),
                ])
                ->withMax('documents', 'downloads_count')
                ->unless(
                    $this->option('all'),
                    fn (Builder $query) => $query->where('last_login_at', '>=', now()->subDays(30))
                )->get(),
            callback: fn (User $user) => $badgeService->syncBadges($user),
            hint: 'This may take some time.'
        );

        return self::SUCCESS;
    }
}
