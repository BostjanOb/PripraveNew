<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate
        {--path= : Output path for the sitemap index/file}
        {--max-tags=20000 : Maximum number of URLs per sitemap file before splitting}';

    protected $description = 'Generate static sitemap files in public storage.';

    public function handle(): int
    {
        $path = $this->outputPath();
        $maxTags = max((int) $this->option('max-tags'), 1);

        File::ensureDirectoryExists(dirname($path));

        $this->deleteExistingSitemapFiles($path);

        Sitemap::create()
            ->maxTagsPerSitemap($maxTags)
            ->add($this->staticPageTags())
            ->add($this->documentTags())
            ->add($this->activeProfileTags())
            ->writeToFile($path);

        $this->info("Sitemap generated: {$path}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, Url>
     */
    private function staticPageTags(): array
    {
        return [
            Url::create(route('home')),
            Url::create(route('browse')),
            Url::create(route('help')),
            Url::create(route('contact')),
            Url::create(route('terms')),
        ];
    }

    /**
     * @return array<int, Url>
     */
    private function documentTags(): array
    {
        return Document::select(['slug', 'updated_at'])
            ->orderBy('id')
            ->get()
            ->map(fn (Document $document): Url => $this->makeUrlTag(
                route('document.show', $document),
                $document->updated_at,
            ))
            ->all();
    }

    /**
     * @return array<int, Url>
     */
    private function activeProfileTags(): array
    {
        return User::select(['slug', 'updated_at'])
            ->where('role', '!=', 'admin')
            ->whereHas('documents')
            ->orderBy('id')
            ->get()
            ->map(fn (User $user): Url => $this->makeUrlTag(
                route('profile.show', $user),
                $user->updated_at,
            ))
            ->all();
    }

    private function makeUrlTag(string $url, Carbon|string|null $lastModified): Url
    {
        $tag = Url::create($url);

        if ($lastModified !== null) {
            $tag->setLastModificationDate(Carbon::parse($lastModified));
        }

        return $tag;
    }

    private function outputPath(): string
    {
        $path = $this->option('path');

        return filled($path) ? (string) $path : public_path('sitemap.xml');
    }

    private function deleteExistingSitemapFiles(string $path): void
    {
        $directory = dirname($path);
        $filename = pathinfo($path, PATHINFO_FILENAME);

        foreach (glob($directory.DIRECTORY_SEPARATOR.$filename.'*.xml') ?: [] as $file) {
            File::delete($file);
        }
    }
}
