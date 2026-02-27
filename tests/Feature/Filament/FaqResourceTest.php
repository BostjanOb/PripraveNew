<?php

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

it('loads the faq resource page', function () {
    $faqs = Faq::factory()->count(3)->create();

    Livewire::test(ManageFaqs::class)
        ->assertOk()
        ->assertCanSeeTableRecords($faqs);
});

it('creates a faq via modal action', function () {
    Livewire::test(ManageFaqs::class)
        ->callAction(TestAction::make(CreateAction::class), data: [
            'question' => 'Novo vprašanje',
            'answer' => 'Nov odgovor',
            'icon' => 'star',
            'icon_background_color' => '#FEF3C7',
            'sort_order' => 1,
        ])
        ->assertNotified();

    expect(Faq::query()->where('question', 'Novo vprašanje')->exists())->toBeTrue();
});

it('updates a faq via modal action', function () {
    $faq = Faq::factory()->create([
        'question' => 'Staro vprašanje',
        'answer' => 'Stari odgovor',
        'icon' => 'download',
        'icon_background_color' => '#D1FAE5',
        'sort_order' => 5,
    ]);

    Livewire::test(ManageFaqs::class)
        ->callAction(TestAction::make(EditAction::class)->table($faq), data: [
            'question' => 'Posodobljeno vprašanje',
            'answer' => 'Posodobljen odgovor',
            'icon' => 'shield-check',
            'icon_background_color' => '#FFE4E6',
            'sort_order' => 2,
        ])
        ->assertNotified();

    expect($faq->fresh()->question)->toBe('Posodobljeno vprašanje');
    expect($faq->fresh()->sort_order)->toBe(2);
});

it('displays records in sort order by default', function () {
    $second = Faq::factory()->create(['question' => 'Drugo', 'sort_order' => 2]);
    $first = Faq::factory()->create(['question' => 'Prvo', 'sort_order' => 1]);

    Livewire::test(ManageFaqs::class)
        ->assertCanSeeTableRecords([$first, $second], inOrder: true);
});
