<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    /** @var array<string, int> key: normalized subject name */
    private array $subjectNameMap = [];

    /** @var array<string, int> key: "{vrsta}:{novaVrsta}" */
    private array $categoryMap = [];

    /**
     * Old pripravaIds that were skipped due to missing subject/grade/category.
     *
     * @var array<int, true>
     */
    private array $skippedDocumentIds = [];

    /** @var array<int, int|null>|null */
    private ?array $singleDocumentFileIds = null;

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

        $this->populateDownloadDailyStats();
        $this->populateUserDownloadsCounts();

        $this->info('Set document user timestamps...');
        $this->syncDocumentUserTimestamps();

        $this->info('Migration complete.');

        return self::SUCCESS;
    }

    private function truncateTables(): void
    {
        $this->info('Truncating destination tables...');

        Schema::disableForeignKeyConstraints();

        foreach ([
            'document_user',
            'download_daily_stats',
            'download_records',
            'ratings',
            'comments',
            'document_files',
            'documents',
            'users',
            'school_type_subject',
            'subjects',
            'grades',
            'school_types',
            'categories',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        Schema::enableForeignKeyConstraints();

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
            ['slug' => 'mesano-gradivo', 'name' => 'Mešano gradivo', 'novaVrsta' => 4, 'sort' => 4],
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

            $this->categoryMap['2:'.$sub['novaVrsta']] = $id;
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
        $pivotCount = 0;

        DB::table('subjects')
            ->select('id', 'name')
            ->orderBy('id')
            ->each(function (object $subject): void {
                $this->subjectNameMap[$this->normalizeSubjectName((string) $subject->name)] = (int) $subject->id;
            });

        foreach ($rows as $row) {
            $schoolTypeId = $this->schoolTypeMap[$row->solaId] ?? null;

            if ($schoolTypeId === null) {
                continue;
            }

            $mapKey = "{$row->predmetId}:{$row->solaId}";

            if (isset($this->subjectMap[$mapKey])) {
                continue;
            }

            $subjectName = trim((string) $row->predmetIme);

            if ($subjectName === '') {
                $this->warn("Subject {$row->predmetId}: empty name, skipping.");

                continue;
            }

            $normalizedSubjectName = $this->normalizeSubjectName($subjectName);

            if (isset($this->subjectNameMap[$normalizedSubjectName])) {
                $id = $this->subjectNameMap[$normalizedSubjectName];
            } else {
                $id = DB::table('subjects')->insertGetId([
                    'name' => $subjectName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $this->subjectNameMap[$normalizedSubjectName] = $id;

                $count++;
            }

            $pivotCount += DB::table('school_type_subject')->insertOrIgnore([
                'school_type_id' => $schoolTypeId,
                'subject_id' => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->subjectMap[$mapKey] = $id;
        }

        $this->info("Subjects: {$count} migrated. Pivot links: {$pivotCount} synced.");
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

                    $slug = $row->uporabnikURL !== '' ? (string) $row->uporabnikURL : 'user-'.$row->uporabnikId;

                    if (isset($seenSlugs[$slug])) {
                        $slug = $slug.'-'.$row->uporabnikId;
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
                        'last_login_at' => $row->zadnjaPrijava,
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

        return $parts[0].'-'.$id.'@'.($parts[1] ?? 'placeholder.priprave.net');
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
            ->leftJoin('tema as t', 'p.temaId', '=', 't.temaId')
            ->select('p.*', 'r.solaId', 't.temaIme')
            ->orderBy('p.pripravaId')
            ->chunk($this->chunk, function ($rows) use (&$count, &$skipped, $now, &$usedSlugs) {
                $inserts = [];

                foreach ($rows as $row) {
                    $gradeId = $this->gradeMap[$row->razredId] ?? null;
                    $schoolTypeId = $this->schoolTypeMap[$row->solaId] ?? null;
                    $subjectKey = "{$row->predmetId}:{$row->solaId}";
                    $subjectId = $this->subjectMap[$subjectKey] ?? null;
                    $categoryKey = $row->vrsta.':'.($row->novaVrsta ?? '');
                    $categoryId = $this->categoryMap[$categoryKey] ?? ($this->categoryMap[$row->vrsta.':'] ?? null);

                    if ($gradeId === null || $schoolTypeId === null || $subjectId === null || $categoryId === null) {
                        $this->skippedDocumentIds[$row->pripravaId] = true;
                        $skipped++;

                        continue;
                    }

                    $slug = $this->makeUniqueSlug((string) $row->pripravaURL, $row->pripravaId, $usedSlugs);
                    $usedSlugs[$slug] = true;
                    $topic = trim((string) ($row->temaIme ?? ''));

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
                        'topic' => $topic !== '' ? $topic : null,
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
            $slug = 'dokument-'.$id;
        }

        if (! isset($usedSlugs[$slug])) {
            return $slug;
        }

        return $slug.'-'.$id;
    }

    private function normalizeSubjectName(string $subjectName): string
    {
        return mb_strtolower(trim($subjectName), 'UTF-8');
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
                        'storage_path' => 'datoteka/'.$row->hash,
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

        $this->recalculateDocumentRatings();

        $this->info('Rating averages recalculated.');
    }

    // -------------------------------------------------------------------------
    // Step 10a: Download records from uporabnik_ima_priprava
    // -------------------------------------------------------------------------

    private function migrateDownloadRecordsFromUserDocuments(): void
    {
        $this->info('Migrating download records from uporabnik_ima_priprava...');

        $total = DB::connection('mysql_old')->table('uporabnik_ima_priprava')->count();
        $this->info("Total records: {$total}");

        $migrated = $this->supportsCrossDatabaseInsert()
            ? $this->migrateDownloadRecordsFromUserDocumentsWithCrossDatabaseInsert()
            : $this->migrateDownloadRecordsFromUserDocumentsWithChunkedFallback();

        $this->info("Download records (user-document): {$migrated} migrated.");
    }

    // -------------------------------------------------------------------------
    // Step 10b: Download records from logPrenos
    // -------------------------------------------------------------------------

    private function migrateDownloadRecordsFromLog(): void
    {
        $this->info('Migrating download records from logPrenos...');

        $total = DB::connection('mysql_old')->table('logPrenos')->count();
        $this->info("Total records: {$total}");

        $migrated = $this->supportsCrossDatabaseInsert()
            ? $this->migrateDownloadRecordsFromLogWithCrossDatabaseInsert()
            : $this->migrateDownloadRecordsFromLogWithChunkedFallback();

        $this->info("Download records (logPrenos): {$migrated} migrated.");
    }

    // -------------------------------------------------------------------------
    // Step 11: Populate derived tables
    // -------------------------------------------------------------------------

    private function populateDownloadDailyStats(): void
    {
        $this->info('Populating download_daily_stats...');

        if (! Schema::hasTable('download_daily_stats')) {
            $this->warn('download_daily_stats table does not exist, skipping.');

            return;
        }

        $isMysql = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
        $upsertClause = $isMysql
            ? 'ON DUPLICATE KEY UPDATE `download_count` = VALUES(`download_count`)'
            : 'ON CONFLICT(`date`) DO UPDATE SET `download_count` = excluded.`download_count`';

        $count = DB::affectingStatement("
            INSERT INTO `download_daily_stats` (`date`, `download_count`)
            SELECT DATE(`created_at`), COUNT(*)
            FROM `download_records`
            GROUP BY DATE(`created_at`)
            {$upsertClause}
        ");

        $this->info("download_daily_stats: {$count} rows populated.");
    }

    private function populateUserDownloadsCounts(): void
    {
        $this->info('Populating users.downloads_count...');

        if (! Schema::hasColumn('users', 'downloads_count')) {
            $this->warn('users.downloads_count column does not exist, skipping.');

            return;
        }

        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('
                UPDATE `users`
                INNER JOIN (
                    SELECT `user_id`, COUNT(*) AS `cnt`
                    FROM `download_records`
                    GROUP BY `user_id`
                ) AS `dr` ON `users`.`id` = `dr`.`user_id`
                SET `users`.`downloads_count` = `dr`.`cnt`
            ');
        } else {
            DB::statement('
                UPDATE `users`
                SET `downloads_count` = (
                    SELECT COUNT(*)
                    FROM `download_records`
                    WHERE `download_records`.`user_id` = `users`.`id`
                )
                WHERE EXISTS (
                    SELECT 1 FROM `download_records`
                    WHERE `download_records`.`user_id` = `users`.`id`
                )
            ');
        }

        $this->info('users.downloads_count populated.');
    }

    private function recalculateDocumentRatings(): void
    {
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
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

            return;
        }

        DB::table('ratings')
            ->selectRaw('document_id, COUNT(*) AS cnt, AVG(rating) AS avg_rating')
            ->groupBy('document_id')
            ->orderBy('document_id')
            ->each(function (object $rating): void {
                DB::table('documents')
                    ->where('id', $rating->document_id)
                    ->update([
                        'rating_count' => (int) $rating->cnt,
                        'rating_avg' => round((float) $rating->avg_rating, 2),
                    ]);
            });
    }

    private function migrateDownloadRecordsFromUserDocumentsWithCrossDatabaseInsert(): int
    {
        $this->info('Using cross-database bulk insert for uporabnik_ima_priprava.');

        $oldDb = DB::connection('mysql_old')->getDatabaseName();

        return DB::affectingStatement("
            INSERT INTO `download_records` (`user_id`, `document_id`, `document_file_id`, `created_at`)
            SELECT `uip`.`uporabnikId`, `uip`.`pripravaId`, NULL, `p`.`datum`
            FROM `{$oldDb}`.`uporabnik_ima_priprava` AS `uip`
            INNER JOIN `{$oldDb}`.`priprava` AS `p` ON `p`.`pripravaId` = `uip`.`pripravaId`
            INNER JOIN `documents` AS `d` ON `d`.`id` = `uip`.`pripravaId`
            INNER JOIN `users` AS `u` ON `u`.`id` = `uip`.`uporabnikId`
            WHERE `uip`.`uporabnikId` > 0
        ");
    }

    private function migrateDownloadRecordsFromLogWithCrossDatabaseInsert(): int
    {
        $this->info('Using cross-database bulk insert for logPrenos.');

        $oldDb = DB::connection('mysql_old')->getDatabaseName();

        return DB::affectingStatement("
            INSERT INTO `download_records` (`user_id`, `document_id`, `document_file_id`, `created_at`)
            SELECT
                `l`.`uporabnikId`,
                `l`.`pripravaId`,
                CASE
                    WHEN `l`.`tip` = 'datoteka' THEN `single_document_files`.`document_file_id`
                    ELSE NULL
                END,
                `l`.`cas`
            FROM `{$oldDb}`.`logPrenos` AS `l`
            INNER JOIN `documents` AS `d` ON `d`.`id` = `l`.`pripravaId`
            INNER JOIN `users` AS `u` ON `u`.`id` = `l`.`uporabnikId`
            LEFT JOIN (
                SELECT `document_id`, MIN(`id`) AS `document_file_id`
                FROM `document_files`
                GROUP BY `document_id`
                HAVING COUNT(*) = 1
            ) AS `single_document_files` ON `single_document_files`.`document_id` = `l`.`pripravaId`
            WHERE `l`.`uporabnikId` > 0
        ");
    }

    private function migrateDownloadRecordsFromUserDocumentsWithChunkedFallback(): int
    {
        $this->info('Using chunked fallback import for uporabnik_ima_priprava.');

        $count = 0;

        DB::connection('mysql_old')
            ->table('uporabnik_ima_priprava')
            ->orderBy('uporabnikId')
            ->orderBy('pripravaId')
            ->chunk($this->chunk, function ($rows) use (&$count): void {
                $documentIds = collect($rows)->pluck('pripravaId')->map(fn (mixed $id): int => (int) $id)->all();
                $userIds = collect($rows)->pluck('uporabnikId')->map(fn (mixed $id): int => (int) $id)->all();

                $existingDocumentIds = $this->existingDocumentIds($documentIds);
                $existingUserIds = $this->existingUserIds($userIds);
                $legacyDocumentDates = $this->legacyDocumentDates($documentIds);

                $records = [];

                foreach ($rows as $row) {
                    $documentId = (int) $row->pripravaId;
                    $userId = (int) $row->uporabnikId;

                    if (
                        $userId <= 0
                        || ! isset($existingDocumentIds[$documentId])
                        || ! isset($existingUserIds[$userId])
                        || ! isset($legacyDocumentDates[$documentId])
                    ) {
                        continue;
                    }

                    $records[] = [
                        'user_id' => $userId,
                        'document_id' => $documentId,
                        'document_file_id' => null,
                        'created_at' => $legacyDocumentDates[$documentId],
                    ];
                }

                $this->insertDownloadRecords($records);
                $count += count($records);
            });

        return $count;
    }

    private function migrateDownloadRecordsFromLogWithChunkedFallback(): int
    {
        $this->info('Using chunked fallback import for logPrenos.');

        $count = 0;
        $singleDocumentFileIds = $this->singleDocumentFileIds();

        DB::connection('mysql_old')
            ->table('logPrenos')
            ->orderBy('id')
            ->chunk($this->chunk, function ($rows) use (&$count, $singleDocumentFileIds): void {
                $documentIds = collect($rows)->pluck('pripravaId')->map(fn (mixed $id): int => (int) $id)->all();
                $userIds = collect($rows)->pluck('uporabnikId')->map(fn (mixed $id): int => (int) $id)->all();

                $existingDocumentIds = $this->existingDocumentIds($documentIds);
                $existingUserIds = $this->existingUserIds($userIds);

                $records = [];

                foreach ($rows as $row) {
                    $documentId = (int) $row->pripravaId;
                    $userId = (int) $row->uporabnikId;

                    if ($userId <= 0 || ! isset($existingDocumentIds[$documentId]) || ! isset($existingUserIds[$userId])) {
                        continue;
                    }

                    $records[] = [
                        'user_id' => $userId,
                        'document_id' => $documentId,
                        'document_file_id' => $row->tip === 'datoteka'
                            ? ($singleDocumentFileIds[$documentId] ?? null)
                            : null,
                        'created_at' => $row->cas,
                    ];
                }

                $this->insertDownloadRecords($records);
                $count += count($records);
            });

        return $count;
    }

    private function supportsCrossDatabaseInsert(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)
            && in_array(DB::connection('mysql_old')->getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function syncDocumentUserTimestamps(): void
    {
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('
                INSERT INTO `document_user` (`user_id`, `document_id`, `created_at`)
                SELECT `user_id`, `document_id`, MIN(`created_at`) AS `created_at`
                FROM `download_records`
                GROUP BY `user_id`, `document_id`
                ON DUPLICATE KEY UPDATE `created_at` = VALUES(`created_at`)
            ');

            return;
        }

        DB::table('download_records')
            ->selectRaw('user_id, document_id, MIN(created_at) AS min_created_at')
            ->groupBy('user_id', 'document_id')
            ->orderBy('user_id')
            ->orderBy('document_id')
            ->chunk(1000, function ($rows): void {
                $payload = collect($rows)
                    ->map(fn (object $row): array => [
                        'user_id' => (int) $row->user_id,
                        'document_id' => (int) $row->document_id,
                        'created_at' => $row->min_created_at,
                    ])
                    ->all();

                DB::table('document_user')->upsert(
                    $payload,
                    ['user_id', 'document_id'],
                    ['created_at'],
                );
            });
    }

    /**
     * @param  array<int, int>  $documentIds
     * @return array<int, string>
     */
    private function legacyDocumentDates(array $documentIds): array
    {
        if ($documentIds === []) {
            return [];
        }

        return DB::connection('mysql_old')
            ->table('priprava')
            ->whereIn('pripravaId', $documentIds)
            ->whereNotNull('datum')
            ->pluck('datum', 'pripravaId')
            ->mapWithKeys(fn (mixed $date, mixed $documentId): array => [(int) $documentId => (string) $date])
            ->all();
    }

    /**
     * @param  array<int, int>  $documentIds
     * @return array<int, true>
     */
    private function existingDocumentIds(array $documentIds): array
    {
        if ($documentIds === []) {
            return [];
        }

        return DB::table('documents')
            ->whereIn('id', $documentIds)
            ->pluck('id')
            ->mapWithKeys(fn (int $documentId): array => [$documentId => true])
            ->all();
    }

    /**
     * @param  array<int, int>  $userIds
     * @return array<int, true>
     */
    private function existingUserIds(array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }

        return DB::table('users')
            ->whereIn('id', $userIds)
            ->pluck('id')
            ->mapWithKeys(fn (int $userId): array => [$userId => true])
            ->all();
    }

    /**
     * @return array<int, int|null>
     */
    private function singleDocumentFileIds(): array
    {
        if ($this->singleDocumentFileIds !== null) {
            return $this->singleDocumentFileIds;
        }

        $this->singleDocumentFileIds = DB::table('document_files')
            ->selectRaw('document_id, MIN(id) AS document_file_id, COUNT(*) AS files_count')
            ->groupBy('document_id')
            ->get()
            ->mapWithKeys(function (object $row): array {
                return [(int) $row->document_id => (int) $row->files_count === 1
                    ? (int) $row->document_file_id
                    : null];
            })
            ->all();

        return $this->singleDocumentFileIds;
    }

    /**
     * @param  array<int, array<string, mixed>>  $records
     */
    private function insertDownloadRecords(array $records): void
    {
        foreach (array_chunk($records, 1000) as $batch) {
            DB::table('download_records')->insert($batch);
        }
    }
}
