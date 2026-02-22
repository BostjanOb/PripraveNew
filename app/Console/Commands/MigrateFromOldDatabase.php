<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateFromOldDatabase extends Command
{
    protected $signature = 'app:migrate-from-old-database
                            {--fresh : Truncate all destination tables before migrating}
                            {--chunk=1000 : Batch insert size}
                            {--skip-downloads : Skip logPrenos migration (18M+ rows)}';

    protected $description = 'Migrate data from the old priprave_old database to the new schema';

    private int $chunk;

    /** @var array<int, int> */
    private array $schoolTypeMap = [];

    /** @var array<int, int> */
    private array $gradeMap = [];

    /** @var array<string, int> key: "{predmetId}:{solaId}" */
    private array $subjectMap = [];

    /** @var array<string, int> key: "{vrsta}:{novaVrsta}" */
    private array $categoryMap = [];

    /**
     * Old pripravaIds that were skipped due to missing subject/grade/category.
     *
     * @var array<int, true>
     */
    private array $skippedDocumentIds = [];

    public function handle(): int
    {
        $this->chunk = (int) $this->option('chunk');

        if ($this->option('fresh')) {
            $this->truncateTables();
        }

        $this->migrateCategories();
        $this->migrateSchoolTypes();
        $this->migrateGrades();
        $this->migrateSubjects();
        $this->migrateUsers();
        $this->migrateDocuments();
        $this->migrateDocumentFiles();
        $this->migrateComments();
        $this->migrateRatings();
        $this->migrateDownloadRecordsFromUserDocuments();

        if (! $this->option('skip-downloads')) {
            $this->migrateDownloadRecordsFromLog();
        } else {
            $this->info('Skipping logPrenos migration (--skip-downloads).');
        }

        $this->info('Migration complete.');

        return self::SUCCESS;
    }

    private function truncateTables(): void
    {
        $this->info('Truncating destination tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'download_records',
            'ratings',
            'comments',
            'document_files',
            'documents',
            'users',
            'subjects',
            'grades',
            'school_types',
            'categories',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Tables truncated.');
    }

    // -------------------------------------------------------------------------
    // Step 1: Categories (hardcoded)
    // -------------------------------------------------------------------------

    private function migrateCategories(): void
    {
        $this->info('Migrating categories...');

        $now = now();

        $pripravaId = DB::table('categories')->insertGetId([
            'name' => 'Priprava',
            'slug' => 'priprava',
            'parent_id' => null,
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ostaloId = DB::table('categories')->insertGetId([
            'name' => 'Ostalo',
            'slug' => 'ostalo',
            'parent_id' => null,
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $subcategories = [
            ['slug' => 'ucni-list', 'name' => 'Učni list', 'novaVrsta' => 1, 'sort' => 1],
            ['slug' => 'preverjanje-znanja', 'name' => 'Preverjanje znanja', 'novaVrsta' => 2, 'sort' => 2],
            ['slug' => 'test', 'name' => 'Test', 'novaVrsta' => 3, 'sort' => 3],
            ['slug' => 'delovni-list', 'name' => 'Delovni list', 'novaVrsta' => 4, 'sort' => 4],
        ];

        foreach ($subcategories as $sub) {
            $id = DB::table('categories')->insertGetId([
                'name' => $sub['name'],
                'slug' => $sub['slug'],
                'parent_id' => $ostaloId,
                'sort_order' => $sub['sort'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->categoryMap['2:' . $sub['novaVrsta']] = $id;
        }

        $this->categoryMap['1:'] = $pripravaId;
        $this->categoryMap['2:'] = $ostaloId;

        $this->info('Categories: 6 created.');
    }

    // -------------------------------------------------------------------------
    // Step 2: School types
    // -------------------------------------------------------------------------

    private function migrateSchoolTypes(): void
    {
        $this->info('Migrating school types...');

        $rows = DB::connection('mysql_old')
            ->table('sola')
            ->orderBy('sort')
            ->get();

        $now = now();
        $count = 0;

        foreach ($rows as $row) {
            $id = DB::table('school_types')->insertGetId([
                'name' => $row->solaIme,
                'slug' => $row->solaURL,
                'sort_order' => $row->sort ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->schoolTypeMap[$row->solaId] = $id;
            $count++;
        }

        $this->info("School types: {$count} migrated.");
    }

    // -------------------------------------------------------------------------
    // Step 3: Grades
    // -------------------------------------------------------------------------

    private function migrateGrades(): void
    {
        $this->info('Migrating grades...');

        $rows = DB::connection('mysql_old')
            ->table('razred')
            ->orderBy('solaId')
            ->orderBy('sort')
            ->get();

        $now = now();
        $count = 0;

        foreach ($rows as $row) {
            $schoolTypeId = $this->schoolTypeMap[$row->solaId] ?? null;

            if ($schoolTypeId === null) {
                $this->warn("Grade {$row->razredId}: unknown solaId {$row->solaId}, skipping.");
                continue;
            }

            $id = DB::table('grades')->insertGetId([
                'school_type_id' => $schoolTypeId,
                'name' => $row->razredIme,
                'sort_order' => $row->sort,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->gradeMap[$row->razredId] = $id;
            $count++;
        }

        $this->info("Grades: {$count} migrated.");
    }

    // -------------------------------------------------------------------------
    // Step 4: Subjects
    // -------------------------------------------------------------------------

    private function migrateSubjects(): void
    {
        $this->info('Migrating subjects...');

        $rows = DB::connection('mysql_old')
            ->table('razred_ima_predmet as rip')
            ->join('razred as r', 'rip.razredId', '=', 'r.razredId')
            ->join('predmet as p', 'rip.predmetId', '=', 'p.predmetId')
            ->select('p.predmetId', 'p.predmetIme', 'r.solaId')
            ->distinct()
            ->orderBy('r.solaId')
            ->orderBy('p.predmetIme')
            ->get();

        $now = now();
        $count = 0;

        foreach ($rows as $row) {
            $schoolTypeId = $this->schoolTypeMap[$row->solaId] ?? null;

            if ($schoolTypeId === null) {
                continue;
            }

            $mapKey = "{$row->predmetId}:{$row->solaId}";

            if (isset($this->subjectMap[$mapKey])) {
                continue;
            }

            $existing = DB::table('subjects')
                ->where('name', $row->predmetIme)
                ->where('school_type_id', $schoolTypeId)
                ->value('id');

            if ($existing) {
                $this->subjectMap[$mapKey] = $existing;
                continue;
            }

            $id = DB::table('subjects')->insertGetId([
                'school_type_id' => $schoolTypeId,
                'name' => $row->predmetIme,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->subjectMap[$mapKey] = $id;
            $count++;
        }

        $this->info("Subjects: {$count} migrated.");
    }

    // -------------------------------------------------------------------------
    // Step 5: Users  (old uporabnikId preserved as new users.id — no map needed)
    // -------------------------------------------------------------------------

    private function migrateUsers(): void
    {
        $this->info('Migrating users...');

        $count = 0;
        $now = now();

        /** @var array<string, bool> */
        $seenEmails = [];

        /** @var array<string, bool> */
        $seenSlugs = [];

        DB::connection('mysql_old')
            ->table('uporabnik')
            ->orderBy('uporabnikId')
            ->chunk($this->chunk, function ($rows) use (&$count, $now, &$seenEmails, &$seenSlugs) {
                $inserts = [];

                foreach ($rows as $row) {
                    $email = trim((string) $row->email);

                    if ($email === '') {
                        $email = "no-email-{$row->uporabnikId}@placeholder.priprave.net";
                    }

                    if (isset($seenEmails[$email])) {
                        $email = $this->makeUniqueEmail($email, $row->uporabnikId);
                    }

                    $seenEmails[$email] = true;

                    $slug = $row->uporabnikURL !== '' ? (string) $row->uporabnikURL : 'user-' . $row->uporabnikId;

                    if (isset($seenSlugs[$slug])) {
                        $slug = $slug . '-' . $row->uporabnikId;
                    }

                    $seenSlugs[$slug] = true;

                    $firstName = trim((string) $row->ime);
                    $lastName = trim((string) $row->priimek);
                    $fullName = trim("{$firstName} {$lastName}");

                    if ($fullName === '') {
                        $fullName = $row->uporabniskoIme;
                    }

                    $registracija = $row->registracija ?? $now;

                    $inserts[] = [
                        'id' => $row->uporabnikId,
                        'display_name' => $row->uporabniskoIme,
                        'slug' => $slug,
                        'name' => $fullName,
                        'email' => $email,
                        'password' => (string) $row->geslo,
                        'role' => $row->vloga === 'admin' ? 'admin' : 'user',
                        'facebook_id' => $row->fb_userid ?: null,
                        'email_verified_at' => $row->emailPotrjen ? $registracija : null,
                        'created_at' => $registracija,
                        'updated_at' => $registracija,
                    ];

                    $count++;
                }

                foreach (array_chunk($inserts, 100) as $batch) {
                    DB::table('users')->insert($batch);
                }
            });

        $this->info("Users: {$count} migrated.");
    }

    private function makeUniqueEmail(string $email, int $id): string
    {
        $parts = explode('@', $email, 2);

        return $parts[0] . '-' . $id . '@' . ($parts[1] ?? 'placeholder.priprave.net');
    }

    // -------------------------------------------------------------------------
    // Step 6: Documents  (old pripravaId preserved as new documents.id — no map needed)
    // -------------------------------------------------------------------------

    private function migrateDocuments(): void
    {
        $this->info('Migrating documents...');

        $count = 0;
        $skipped = 0;
        $now = now();

        /** @var array<string, bool> */
        $usedSlugs = [];

        DB::connection('mysql_old')
            ->table('priprava as p')
            ->join('razred as r', 'p.razredId', '=', 'r.razredId')
            ->select('p.*', 'r.solaId')
            ->orderBy('p.pripravaId')
            ->chunk($this->chunk, function ($rows) use (&$count, &$skipped, $now, &$usedSlugs) {
                $inserts = [];

                foreach ($rows as $row) {
                    $gradeId = $this->gradeMap[$row->razredId] ?? null;
                    $schoolTypeId = $this->schoolTypeMap[$row->solaId] ?? null;
                    $subjectKey = "{$row->predmetId}:{$row->solaId}";
                    $subjectId = $this->subjectMap[$subjectKey] ?? null;
                    $categoryKey = $row->vrsta . ':' . ($row->novaVrsta ?? '');
                    $categoryId = $this->categoryMap[$categoryKey] ?? ($this->categoryMap[$row->vrsta . ':'] ?? null);

                    if ($gradeId === null || $schoolTypeId === null || $subjectId === null || $categoryId === null) {
                        $this->skippedDocumentIds[$row->pripravaId] = true;
                        $skipped++;
                        continue;
                    }

                    $slug = $this->makeUniqueSlug((string) $row->pripravaURL, $row->pripravaId, $usedSlugs);
                    $usedSlugs[$slug] = true;

                    $inserts[] = [
                        'id' => $row->pripravaId,
                        'user_id' => $row->uporabnikId,
                        'category_id' => $categoryId,
                        'school_type_id' => $schoolTypeId,
                        'grade_id' => $gradeId,
                        'subject_id' => $subjectId,
                        'title' => $row->naslov,
                        'slug' => $slug,
                        'description' => $row->opis,
                        'topic' => null,
                        'keywords' => $row->kljucneBesede,
                        'downloads_count' => $row->stPrenosov ?? 0,
                        'views_count' => 0,
                        'rating_avg' => null,
                        'rating_count' => 0,
                        'deleted_at' => null,
                        'created_at' => $row->datum ?? $now,
                        'updated_at' => $row->datum ?? $now,
                    ];

                    $count++;
                }

                foreach (array_chunk($inserts, 100) as $batch) {
                    DB::table('documents')->insert($batch);
                }
            });

        $this->info("Documents: {$count} migrated, {$skipped} skipped.");
    }

    private function makeUniqueSlug(string $slug, int $id, array $usedSlugs): string
    {
        if ($slug === '') {
            $slug = 'dokument-' . $id;
        }

        if (! isset($usedSlugs[$slug])) {
            return $slug;
        }

        return $slug . '-' . $id;
    }

    // -------------------------------------------------------------------------
    // Step 7: Document files
    // -------------------------------------------------------------------------

    private function migrateDocumentFiles(): void
    {
        $this->info('Migrating document files...');

        $count = 0;
        $skipped = 0;
        $now = now();

        DB::connection('mysql_old')
            ->table('datoteka')
            ->orderBy('datotekaId')
            ->chunk($this->chunk, function ($rows) use (&$count, &$skipped, $now) {
                $inserts = [];

                foreach ($rows as $row) {
                    if (isset($this->skippedDocumentIds[$row->pripravaId])) {
                        $skipped++;
                        continue;
                    }

                    $rawExt = pathinfo($row->datotekaIme, PATHINFO_EXTENSION);
                    $extension = strlen($rawExt) <= 10 ? strtolower($rawExt) : '';

                    $inserts[] = [
                        'document_id' => $row->pripravaId,
                        'original_name' => $row->datotekaIme,
                        'storage_path' => 'datoteka/' . $row->hash,
                        'size_bytes' => $row->velikost,
                        'mime_type' => $row->tip,
                        'extension' => $extension,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $count++;
                }

                foreach (array_chunk($inserts, 100) as $batch) {
                    DB::table('document_files')->insert($batch);
                }
            });

        $this->info("Document files: {$count} migrated, {$skipped} skipped.");
    }

    // -------------------------------------------------------------------------
    // Step 8: Comments
    // -------------------------------------------------------------------------

    private function migrateComments(): void
    {
        $this->info('Migrating comments...');

        $count = 0;
        $skipped = 0;

        DB::connection('mysql_old')
            ->table('komentar')
            ->orderBy('komentarId')
            ->chunk($this->chunk, function ($rows) use (&$count, &$skipped) {
                $inserts = [];

                foreach ($rows as $row) {
                    if (! $row->uporabnikId || isset($this->skippedDocumentIds[$row->pripravaId])) {
                        $skipped++;
                        continue;
                    }

                    $inserts[] = [
                        'document_id' => $row->pripravaId,
                        'user_id' => $row->uporabnikId,
                        'text' => (string) $row->besedilo,
                        'created_at' => $row->datumOddaje,
                        'updated_at' => $row->datumOddaje,
                    ];

                    $count++;
                }

                foreach (array_chunk($inserts, 100) as $batch) {
                    DB::table('comments')->insert($batch);
                }
            });

        $this->info("Comments: {$count} migrated, {$skipped} skipped.");
    }

    // -------------------------------------------------------------------------
    // Step 9: Ratings
    // -------------------------------------------------------------------------

    private function migrateRatings(): void
    {
        $this->info('Migrating ratings...');

        $count = 0;
        $skipped = 0;
        $now = now();

        DB::connection('mysql_old')
            ->table('ocena')
            ->orderBy('uporabnikId')
            ->chunk($this->chunk, function ($rows) use (&$count, &$skipped, $now) {
                $inserts = [];

                foreach ($rows as $row) {
                    if (! $row->uporabnikId || isset($this->skippedDocumentIds[$row->pripravaId])) {
                        $skipped++;
                        continue;
                    }

                    $inserts[] = [
                        'document_id' => $row->pripravaId,
                        'user_id' => $row->uporabnikId,
                        'rating' => (int) $row->ocena,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $count++;
                }

                foreach (array_chunk($inserts, 100) as $batch) {
                    DB::table('ratings')->insertOrIgnore($batch);
                }
            });

        $this->info("Ratings: {$count} migrated, {$skipped} skipped.");

        $this->info('Recalculating document rating averages...');

        DB::statement('
            UPDATE documents d
            JOIN (
                SELECT document_id, COUNT(*) AS cnt, AVG(rating) AS avg_rating
                FROM ratings
                GROUP BY document_id
            ) r ON d.id = r.document_id
            SET d.rating_count = r.cnt,
                d.rating_avg   = ROUND(r.avg_rating, 2)
        ');

        $this->info('Rating averages recalculated.');
    }

    // -------------------------------------------------------------------------
    // Step 10a: Download records from uporabnik_ima_priprava
    // -------------------------------------------------------------------------

    private function migrateDownloadRecordsFromUserDocuments(): void
    {
        $this->info('Migrating download records from uporabnik_ima_priprava...');

        $total = DB::connection('mysql_old')->table('uporabnik_ima_priprava')->count();
        $this->info("Total records: {$total} — running direct SQL insert (no row-by-row progress)...");

        // Use a single cross-database INSERT...SELECT. MySQL handles everything
        // server-side — no PHP loop, no round-trips, orders of magnitude faster
        // than chunked inserts for large tables.
        // The INNER JOIN on `documents` automatically excludes skipped documents.
        $oldDb = DB::connection('mysql_old')->getDatabaseName();

        $migrated = DB::affectingStatement("
            INSERT INTO `download_records` (`user_id`, `document_id`, `created_at`)
            SELECT `uip`.`uporabnikId`, `uip`.`pripravaId`, `p`.`datum`
            FROM `{$oldDb}`.`uporabnik_ima_priprava` AS `uip`
            INNER JOIN `{$oldDb}`.`priprava` AS `p` ON `p`.`pripravaId` = `uip`.`pripravaId`
            INNER JOIN `documents` AS `d` ON `d`.`id` = `uip`.`pripravaId`
            WHERE `uip`.`uporabnikId` > 0
        ");

        $this->info("Download records (user-document): {$migrated} migrated.");
    }

    // -------------------------------------------------------------------------
    // Step 10b: Download records from logPrenos
    // -------------------------------------------------------------------------

    private function migrateDownloadRecordsFromLog(): void
    {
        $this->info('Migrating download records from logPrenos...');

        $total = DB::connection('mysql_old')->table('logPrenos')->count();
        $this->info("Total records: {$total} — running direct SQL insert (no row-by-row progress)...");

        $oldDb = DB::connection('mysql_old')->getDatabaseName();

        $migrated = DB::affectingStatement("
            INSERT INTO `download_records` (`user_id`, `document_id`, `created_at`)
            SELECT `l`.`uporabnikId`, `l`.`pripravaId`, `l`.`cas`
            FROM `{$oldDb}`.`logPrenos` AS `l`
            INNER JOIN `documents` AS `d` ON `d`.`id` = `l`.`pripravaId`
            WHERE `l`.`uporabnikId` > 0
        ");

        $this->info("Download records (logPrenos): {$migrated} migrated.");
    }
}
