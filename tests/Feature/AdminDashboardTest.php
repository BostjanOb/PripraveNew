<?php

use App\Filament\Widgets\AdminOverviewStats;
use App\Filament\Widgets\DownloadsChart;
use App\Filament\Widgets\LatestDocumentsTable;
use App\Filament\Widgets\NewRegisteredUsersChart;
use App\Filament\Widgets\NewUploadsChart;
use App\Models\Document;
use App\Models\DownloadRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-03-12 12:00:00'));
    Cache::flush();

    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

afterEach(function () {
    Carbon::setTestNow();
    Cache::flush();
});

it('loads the admin dashboard page', function () {
    $this->get('/admin')
        ->assertSuccessful()
        ->assertSee('Nadzorna plošča');
});

it('denies non-admin users from accessing admin dashboard', function () {
    $this->actingAs(User::factory()->create(['role' => 'user']))
        ->get('/admin')
        ->assertForbidden();
});

it('renders dashboard chart widgets', function () {
    Livewire::test(NewUploadsChart::class)
        ->assertOk()
        ->assertSee('Nova gradiva');

    Livewire::test(DownloadsChart::class)
        ->assertOk()
        ->assertSee('Prenosi');

    Livewire::test(NewRegisteredUsersChart::class)
        ->assertOk()
        ->assertSee('Novi uporabniki');
});

it('shows overview stats with seeded totals', function () {
    User::factory()->create([
        'role' => 'user',
        'last_login_at' => now()->subDays(1),
    ]);
    User::factory()->create([
        'role' => 'user',
        'last_login_at' => now()->subDays(12),
    ]);
    User::factory()->create([
        'role' => 'user',
        'last_login_at' => now()->subDays(45),
    ]);
    User::factory()->create([
        'role' => 'admin',
        'last_login_at' => now()->subDays(2),
    ]);

    $recentDocuments = Document::factory()->count(3)->create([
        'created_at' => now()->subDays(5),
        'downloads_count' => 2,
    ]);

    Document::factory()->create([
        'created_at' => now()->subDays(45),
        'downloads_count' => 5,
    ]);

    foreach (range(1, 7) as $index) {
        DownloadRecord::factory()->create([
            'document_id' => $recentDocuments[$index % $recentDocuments->count()]->id,
            'created_at' => now()->subDays(3),
        ]);
    }

    Livewire::test(AdminOverviewStats::class)
        ->assertOk()
        ->assertSee('Aktivni uporabniki')
        ->assertSee('+3 v zadnjih 30 dneh')
        ->assertSee('+7 v zadnjih 30 dneh')
        ->assertSee('+2 v zadnjih 30 dneh');
});

it('shows the latest non-deleted documents in descending order', function () {
    $documents = collect(range(1, 12))
        ->map(function (int $index): Document {
            return Document::factory()->create([
                'title' => sprintf('Gradivo %02d', $index),
                'created_at' => now()->subMinutes(12 - $index),
            ]);
        });

    $documents->last()->delete();

    $expectedDocuments = $documents
        ->slice(1, 10)
        ->reverse()
        ->values();

    Livewire::test(LatestDocumentsTable::class)
        ->assertOk()
        ->assertCanSeeTableRecords($expectedDocuments, inOrder: true)
        ->assertCanNotSeeTableRecords([$documents->first(), $documents->last()]);
});
