<?php

use App\Livewire\LatestDocuments;
use App\Models\Document;
use App\Models\SchoolType;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Homepage ──────────────────────────────────────────────────────────────────

it('loads the homepage successfully', function () {
    SchoolType::factory()->count(3)->create();

    $this->get(route('home'))
        ->assertSuccessful();
});

it('uses flux appearance controls for dark mode', function () {
    SchoolType::factory()->count(3)->create();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee("\$flux.appearance = 'light'", false)
        ->assertSee("\$flux.appearance = 'dark'", false)
        ->assertSee("\$flux.appearance = 'system'", false)
        ->assertDontSee("localStorage.getItem('dark')", false)
        ->assertDontSee("localStorage.setItem('dark'", false)
        ->assertDontSee('isDark = !isDark', false);
});

it('displays the hero search section', function () {
    SchoolType::factory()->count(3)->create();

    $this->get(route('home'))
        ->assertSee('Priprave za pouk')
        ->assertSee('hitro najdi');
});

it('displays category cards with school types', function () {
    $pv = SchoolType::factory()->create(['name' => 'predšolska vzgoja', 'slug' => 'pv', 'sort_order' => 1]);
    $os = SchoolType::factory()->create(['name' => 'osnovna šola', 'slug' => 'os', 'sort_order' => 2]);
    $ss = SchoolType::factory()->create(['name' => 'srednja šola', 'slug' => 'ss', 'sort_order' => 3]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Predšolska vzgoja')
        ->assertSee('Osnovna šola')
        ->assertSee('Srednja šola');
});

it('displays the upload CTA section', function () {
    SchoolType::factory()->count(3)->create();

    $this->get(route('home'))
        ->assertSee('Deli jo s kolegi!');
});

it('displays the stats section', function () {
    SchoolType::factory()->count(3)->create();
    Document::factory()->count(3)->create();

    $this->get(route('home'))
        ->assertSee('Skupnost, ki raste');
});

it('shows admin panel link in user dropdown for admin users', function () {
    SchoolType::factory()->count(3)->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Admin panel')
        ->assertSee('href="'.url('/admin').'"', false);
});

it('does not show admin panel link in user dropdown for non-admin users', function () {
    SchoolType::factory()->count(3)->create();
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('Admin panel');
});

// ── LatestDocuments Livewire component ────────────────────────────────────────

it('shows latest documents', function () {
    $doc = Document::factory()->create(['title' => 'Testna priprava za matematiko']);

    Livewire::test(LatestDocuments::class)
        ->assertSee('Testna priprava za matematiko');
});

it('filters documents by school type', function () {
    $pv = SchoolType::factory()->create(['name' => 'predšolska vzgoja', 'slug' => 'pv', 'sort_order' => 1]);
    $os = SchoolType::factory()->create(['name' => 'osnovna šola', 'slug' => 'os', 'sort_order' => 2]);

    $pvDoc = Document::factory()->create(['title' => 'Vrtčevska priprava', 'school_type_id' => $pv->id]);
    $osDoc = Document::factory()->create(['title' => 'Osnovna priprava', 'school_type_id' => $os->id]);

    Livewire::test(LatestDocuments::class)
        ->assertSee('Vrtčevska priprava')
        ->assertSee('Osnovna priprava')
        ->call('setActiveType', 'pv')
        ->assertSee('Vrtčevska priprava')
        ->assertDontSee('Osnovna priprava')
        ->call('setActiveType', 'all')
        ->assertSee('Vrtčevska priprava')
        ->assertSee('Osnovna priprava');
});

it('shows the results count', function () {
    Document::factory()->count(5)->create();

    Livewire::test(LatestDocuments::class)
        ->assertSee('5 rezultatov');
});
