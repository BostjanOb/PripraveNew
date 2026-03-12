<?php

use App\Models\Document;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('migrates legacy data with subject-school links, topics, ratings, downloads, and flagged documents', function () {
    $legacyDatabasePath = tempnam(sys_get_temp_dir(), 'legacy-priprave-');

    expect($legacyDatabasePath)->not->toBeFalse();

    configureLegacyConnection($legacyDatabasePath);
    createLegacySchema();
    seedLegacyData();

    try {
        $this->artisan('app:migrate-from-old-database', ['--fresh' => true, '--chunk' => 2])
            ->expectsOutputToContain('Using chunked fallback import for uporabnik_ima_priprava.')
            ->expectsOutputToContain('Using chunked fallback import for logPrenos.')
            ->assertSuccessful();

        $sharedSubject = Subject::query()->where('name', 'Matematika')->firstOrFail();

        expect(Subject::query()->count())->toBe(1);
        expect(DB::table('school_type_subject')->count())->toBe(2);

        $firstDocument = Document::query()->findOrFail(1000);
        $secondDocument = Document::query()->findOrFail(1001);

        expect($firstDocument->subject_id)->toBe($sharedSubject->id)
            ->and($secondDocument->subject_id)->toBe($sharedSubject->id)
            ->and($firstDocument->topic)->toBe('Seštevanje do 100')
            ->and($secondDocument->topic)->toBe('Besedila in pomen')
            ->and($secondDocument->deleted_at)->toBeNull()
            ->and($secondDocument->downloads_count)->toBe(7)
            ->and($firstDocument->rating_count)->toBe(1)
            ->and((float) $firstDocument->rating_avg)->toBe(5.0);

        $this->assertDatabaseHas('comments', [
            'document_id' => 1000,
            'user_id' => 2,
            'text' => 'Uporaben komentar',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'email' => 'ana@example.test',
            'last_login_at' => '2024-02-10 06:15:00',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => 2,
            'email' => 'bojan@example.test',
            'last_login_at' => null,
        ]);

        $this->assertDatabaseHas('document_files', [
            'document_id' => 1000,
            'original_name' => 'delovni-list.pdf',
            'storage_path' => 'datoteka/hash-1000',
            'extension' => 'pdf',
        ]);

        $firstDocumentFileId = DB::table('document_files')
            ->where('document_id', 1000)
            ->value('id');

        expect(DB::table('download_records')->count())->toBe(5);
        expect($firstDocumentFileId)->not->toBeNull();

        $this->assertDatabaseHas('download_records', [
            'user_id' => 2,
            'document_id' => 1000,
            'document_file_id' => null,
            'created_at' => '2024-01-10 08:00:00',
        ]);

        $this->assertDatabaseHas('download_records', [
            'user_id' => 2,
            'document_id' => 1000,
            'document_file_id' => $firstDocumentFileId,
            'created_at' => '2024-01-12 10:00:00',
        ]);

        $this->assertDatabaseHas('download_records', [
            'user_id' => 1,
            'document_id' => 1001,
            'document_file_id' => null,
            'created_at' => '2024-01-13 11:00:00',
        ]);

        $this->assertDatabaseHas('download_records', [
            'user_id' => 2,
            'document_id' => 1001,
            'document_file_id' => null,
            'created_at' => '2024-01-13 12:00:00',
        ]);
    } finally {
        DB::disconnect('mysql_old');

        if (is_string($legacyDatabasePath) && file_exists($legacyDatabasePath)) {
            unlink($legacyDatabasePath);
        }
    }
});

function configureLegacyConnection(string $legacyDatabasePath): void
{
    config()->set('database.connections.mysql_old', [
        'driver' => 'sqlite',
        'database' => $legacyDatabasePath,
        'prefix' => '',
        'foreign_key_constraints' => true,
        'busy_timeout' => null,
        'journal_mode' => null,
        'synchronous' => null,
        'transaction_mode' => 'DEFERRED',
    ]);

    DB::purge('mysql_old');
    DB::reconnect('mysql_old');
}

function createLegacySchema(): void
{
    Schema::connection('mysql_old')->create('sola', function ($table): void {
        $table->integer('solaId')->primary();
        $table->string('solaIme', 45);
        $table->string('solaURL', 45);
        $table->smallInteger('sort')->nullable();
    });

    Schema::connection('mysql_old')->create('razred', function ($table): void {
        $table->integer('razredId')->primary();
        $table->integer('solaId');
        $table->string('razredIme', 45);
        $table->string('razredURL', 45)->nullable();
        $table->smallInteger('sort')->nullable();
    });

    Schema::connection('mysql_old')->create('predmet', function ($table): void {
        $table->integer('predmetId')->primary();
        $table->string('predmetIme', 45);
        $table->string('predmetKratica', 4)->nullable();
        $table->string('predmetURL', 45)->nullable();
    });

    Schema::connection('mysql_old')->create('razred_ima_predmet', function ($table): void {
        $table->integer('razredId');
        $table->integer('predmetId');
    });

    Schema::connection('mysql_old')->create('uporabnik', function ($table): void {
        $table->integer('uporabnikId')->primary();
        $table->integer('kjeSteIzvedeliId')->nullable();
        $table->string('fb_userid', 30)->nullable();
        $table->string('uporabniskoIme', 45);
        $table->string('geslo', 32);
        $table->string('ime', 50)->nullable();
        $table->string('priimek', 50)->nullable();
        $table->string('email', 255)->nullable();
        $table->unsignedTinyInteger('emailPotrjen')->default(0);
        $table->dateTime('registracija')->nullable();
        $table->dateTime('zadnjaPrijava')->nullable();
        $table->string('vloga', 20)->default('uporabnik');
        $table->string('uporabnikURL', 45)->nullable();
    });

    Schema::connection('mysql_old')->create('tema', function ($table): void {
        $table->integer('temaId')->primary();
        $table->integer('predmetId')->nullable();
        $table->string('temaIme', 255);
        $table->string('temaURL', 255)->nullable();
    });

    Schema::connection('mysql_old')->create('priprava', function ($table): void {
        $table->integer('pripravaId')->primary();
        $table->integer('razredId');
        $table->integer('uporabnikId');
        $table->integer('predmetId');
        $table->integer('temaId')->nullable();
        $table->string('naslov', 255);
        $table->text('opis')->nullable();
        $table->string('kljucneBesede', 255)->nullable();
        $table->dateTime('datum')->nullable();
        $table->integer('stPrenosov')->default(0);
        $table->string('pripravaURL', 255);
        $table->string('ipUporabnika', 15)->nullable();
        $table->smallInteger('neprimerna')->default(0);
        $table->tinyInteger('vrsta')->default(1);
        $table->boolean('pregledano')->default(false);
        $table->smallInteger('novaVrsta')->nullable();
    });

    Schema::connection('mysql_old')->create('datoteka', function ($table): void {
        $table->integer('datotekaId')->primary();
        $table->integer('pripravaId');
        $table->string('datotekaIme', 255);
        $table->string('tip', 150)->nullable();
        $table->integer('velikost')->default(0);
        $table->string('hash', 40);
        $table->boolean('predogled')->default(false);
    });

    Schema::connection('mysql_old')->create('komentar', function ($table): void {
        $table->integer('komentarId')->primary();
        $table->integer('uporabnikId');
        $table->integer('pripravaId');
        $table->text('besedilo');
        $table->dateTime('datumOddaje');
    });

    Schema::connection('mysql_old')->create('ocena', function ($table): void {
        $table->integer('uporabnikId');
        $table->integer('pripravaId');
        $table->tinyInteger('ocena');
    });

    Schema::connection('mysql_old')->create('uporabnik_ima_priprava', function ($table): void {
        $table->integer('uporabnikId');
        $table->integer('pripravaId');
        $table->smallInteger('steviloPrenosov')->default(1);
    });

    Schema::connection('mysql_old')->create('logPrenos', function ($table): void {
        $table->increments('id');
        $table->integer('pripravaId');
        $table->integer('uporabnikId');
        $table->dateTime('cas');
        $table->string('tip', 20);
    });
}

function seedLegacyData(): void
{
    DB::connection('mysql_old')->table('sola')->insert([
        ['solaId' => 1, 'solaIme' => 'Osnovna šola', 'solaURL' => 'os', 'sort' => 1],
        ['solaId' => 2, 'solaIme' => 'Srednja šola', 'solaURL' => 'ss', 'sort' => 2],
    ]);

    DB::connection('mysql_old')->table('razred')->insert([
        ['razredId' => 10, 'solaId' => 1, 'razredIme' => '3. razred', 'razredURL' => '3-razred', 'sort' => 3],
        ['razredId' => 20, 'solaId' => 2, 'razredIme' => '1. letnik', 'razredURL' => '1-letnik', 'sort' => 1],
    ]);

    DB::connection('mysql_old')->table('predmet')->insert([
        ['predmetId' => 100, 'predmetIme' => 'Matematika', 'predmetKratica' => 'MAT', 'predmetURL' => 'matematika-os'],
        ['predmetId' => 200, 'predmetIme' => 'Matematika', 'predmetKratica' => 'MAT', 'predmetURL' => 'matematika-ss'],
    ]);

    DB::connection('mysql_old')->table('razred_ima_predmet')->insert([
        ['razredId' => 10, 'predmetId' => 100],
        ['razredId' => 20, 'predmetId' => 200],
    ]);

    DB::connection('mysql_old')->table('uporabnik')->insert([
        [
            'uporabnikId' => 1,
            'fb_userid' => null,
            'uporabniskoIme' => 'ucitelj.prvi',
            'geslo' => md5('legacy-password-1'),
            'ime' => 'Ana',
            'priimek' => 'Novak',
            'email' => 'ana@example.test',
            'emailPotrjen' => 1,
            'registracija' => '2024-01-01 08:00:00',
            'zadnjaPrijava' => '2024-02-10 06:15:00',
            'vloga' => 'admin',
            'uporabnikURL' => 'ana-novak',
        ],
        [
            'uporabnikId' => 2,
            'fb_userid' => 'fb-legacy-2',
            'uporabniskoIme' => 'ucitelj.drugi',
            'geslo' => md5('legacy-password-2'),
            'ime' => 'Bojan',
            'priimek' => 'Kranjc',
            'email' => 'bojan@example.test',
            'emailPotrjen' => 0,
            'registracija' => '2024-01-02 09:00:00',
            'zadnjaPrijava' => null,
            'vloga' => 'uporabnik',
            'uporabnikURL' => 'bojan-kranjc',
        ],
    ]);

    DB::connection('mysql_old')->table('tema')->insert([
        ['temaId' => 1000, 'predmetId' => 100, 'temaIme' => 'Seštevanje do 100', 'temaURL' => 'sestevanje-do-100'],
        ['temaId' => 2000, 'predmetId' => 200, 'temaIme' => 'Besedila in pomen', 'temaURL' => 'besedila-in-pomen'],
    ]);

    DB::connection('mysql_old')->table('priprava')->insert([
        [
            'pripravaId' => 1000,
            'razredId' => 10,
            'uporabnikId' => 1,
            'predmetId' => 100,
            'temaId' => 1000,
            'naslov' => 'Prva priprava',
            'opis' => 'Opis prve priprave.',
            'kljucneBesede' => 'matematika, sestevanje',
            'datum' => '2024-01-10 08:00:00',
            'stPrenosov' => 5,
            'pripravaURL' => 'prva-priprava',
            'ipUporabnika' => '127.0.0.1',
            'neprimerna' => 0,
            'vrsta' => 1,
            'pregledano' => 1,
            'novaVrsta' => null,
        ],
        [
            'pripravaId' => 1001,
            'razredId' => 20,
            'uporabnikId' => 2,
            'predmetId' => 200,
            'temaId' => 2000,
            'naslov' => 'Druga priprava',
            'opis' => 'Opis druge priprave.',
            'kljucneBesede' => 'matematika, besedila',
            'datum' => '2024-01-11 09:30:00',
            'stPrenosov' => 7,
            'pripravaURL' => 'druga-priprava',
            'ipUporabnika' => '127.0.0.2',
            'neprimerna' => 1,
            'vrsta' => 2,
            'pregledano' => 0,
            'novaVrsta' => 2,
        ],
    ]);

    DB::connection('mysql_old')->table('datoteka')->insert([
        [
            'datotekaId' => 1,
            'pripravaId' => 1000,
            'datotekaIme' => 'delovni-list.pdf',
            'tip' => 'application/pdf',
            'velikost' => 2048,
            'hash' => 'hash-1000',
            'predogled' => 0,
        ],
        [
            'datotekaId' => 2,
            'pripravaId' => 1001,
            'datotekaIme' => 'priloga-1.pdf',
            'tip' => 'application/pdf',
            'velikost' => 1024,
            'hash' => 'hash-1001-a',
            'predogled' => 0,
        ],
        [
            'datotekaId' => 3,
            'pripravaId' => 1001,
            'datotekaIme' => 'priloga-2.pdf',
            'tip' => 'application/pdf',
            'velikost' => 2048,
            'hash' => 'hash-1001-b',
            'predogled' => 0,
        ],
    ]);

    DB::connection('mysql_old')->table('komentar')->insert([
        [
            'komentarId' => 1,
            'uporabnikId' => 2,
            'pripravaId' => 1000,
            'besedilo' => 'Uporaben komentar',
            'datumOddaje' => '2024-01-12 07:30:00',
        ],
    ]);

    DB::connection('mysql_old')->table('ocena')->insert([
        ['uporabnikId' => 2, 'pripravaId' => 1000, 'ocena' => 5],
    ]);

    DB::connection('mysql_old')->table('uporabnik_ima_priprava')->insert([
        ['uporabnikId' => 2, 'pripravaId' => 1000, 'steviloPrenosov' => 1],
        ['uporabnikId' => 1, 'pripravaId' => 1001, 'steviloPrenosov' => 1],
        ['uporabnikId' => 999, 'pripravaId' => 1000, 'steviloPrenosov' => 1],
    ]);

    DB::connection('mysql_old')->table('logPrenos')->insert([
        ['pripravaId' => 1000, 'uporabnikId' => 2, 'cas' => '2024-01-12 10:00:00', 'tip' => 'datoteka'],
        ['pripravaId' => 1001, 'uporabnikId' => 1, 'cas' => '2024-01-13 11:00:00', 'tip' => 'priprava'],
        ['pripravaId' => 1001, 'uporabnikId' => 2, 'cas' => '2024-01-13 12:00:00', 'tip' => 'datoteka'],
        ['pripravaId' => 1000, 'uporabnikId' => 999, 'cas' => '2024-01-14 12:00:00', 'tip' => 'priprava'],
    ]);
}
