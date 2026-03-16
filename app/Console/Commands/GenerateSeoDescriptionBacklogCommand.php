<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;

class GenerateSeoDescriptionBacklogCommand extends Command
{
    protected $signature = 'seo:description-backfill {--limit=50 : Number of documents to include}';

    protected $description = 'Show the highest-priority documents missing SEO descriptions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $documents = Document::with(['schoolType', 'grade', 'subject', 'category'])
            ->where(function ($query): void {
                $query->whereNull('description')->orWhere('description', '');
            })
            ->orderByDesc('downloads_count')
            ->orderByDesc('views_count')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($documents->isEmpty()) {
            $this->info('All indexed documents already have descriptions.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Naslov', 'Prenosi', 'Ogledi', 'Kontekst'],
            $documents->map(fn (Document $document): array => [
                $document->id,
                $document->title,
                $document->downloads_count,
                $document->views_count,
                collect([
                    $document->category?->name,
                    $document->subject?->name,
                    $document->grade?->name,
                    $document->schoolType?->name,
                ])->filter()->implode(' / '),
            ])->all(),
        );

        return self::SUCCESS;
    }
}
