<?php

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads the pomoc page with faq records', function () {
    Faq::factory()->create([
        'question' => 'Kako najdem učno pripravo?',
        'answer' => 'Uporabite iskalno vrstico.',
        'sort_order' => 1,
    ]);

    Faq::factory()->create([
        'question' => 'Kako naložim svojo pripravo?',
        'answer' => 'Kliknite Dodaj pripravo.',
        'sort_order' => 2,
    ]);

    $this->get(route('help'))
        ->assertSuccessful()
        ->assertSee('Pomoč')
        ->assertSee('Kako najdem učno pripravo?')
        ->assertSee('Kako naložim svojo pripravo?');
});

it('shows faqs in sort order', function () {
    Faq::factory()->create([
        'question' => 'Drugo vprašanje',
        'sort_order' => 2,
    ]);

    Faq::factory()->create([
        'question' => 'Prvo vprašanje',
        'sort_order' => 1,
    ]);

    $this->get(route('help'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Prvo vprašanje', 'Drugo vprašanje']);
});
