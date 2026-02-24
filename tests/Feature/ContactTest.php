<?php

use App\Events\ContactMessageSubmitted;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('loads the contact page', function () {
    $this->get(route('contact'))
        ->assertSuccessful()
        ->assertSee('Kontakt')
        ->assertSee('Kontaktni podatki')
        ->assertSee('Pošlji sporočilo');
});

it('validates required fields', function () {
    Livewire::test('contact-form')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'message']);
});

it('validates email format', function () {
    Livewire::test('contact-form')
        ->set('name', 'Test')
        ->set('email', 'not-an-email')
        ->set('subject', 'Zadeva')
        ->set('message', 'Sporočilo')
        ->call('submit')
        ->assertHasErrors(['email']);
});

it('validates max lengths', function () {
    Livewire::test('contact-form')
        ->set('name', str_repeat('a', 101))
        ->set('email', 'test@example.com')
        ->set('subject', str_repeat('b', 201))
        ->set('message', 'Sporočilo')
        ->call('submit')
        ->assertHasErrors(['name', 'subject']);
});

it('submits the contact form and saves to database', function () {
    Event::fake([ContactMessageSubmitted::class]);

    Livewire::test('contact-form')
        ->set('name', 'Ana Novak')
        ->set('email', 'ana@example.com')
        ->set('subject', 'Testna zadeva')
        ->set('message', 'To je testno sporočilo.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('isSubmitted', true)
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('subject', '')
        ->assertSet('message', '');

    expect(ContactMessage::query()->where('email', 'ana@example.com')->exists())->toBeTrue();

    $message = ContactMessage::query()->where('email', 'ana@example.com')->first();
    expect($message->name)->toBe('Ana Novak');
    expect($message->subject)->toBe('Testna zadeva');
    expect($message->message)->toBe('To je testno sporočilo.');

    Event::assertDispatched(ContactMessageSubmitted::class, function (ContactMessageSubmitted $event) use ($message): bool {
        return $event->contactMessage->id === $message->id;
    });
});

it('shows success message after submission', function () {
    Event::fake([ContactMessageSubmitted::class]);

    Livewire::test('contact-form')
        ->set('name', 'Ana')
        ->set('email', 'ana@example.com')
        ->set('subject', 'Test')
        ->set('message', 'Sporočilo')
        ->call('submit')
        ->assertSee('Sporočilo poslano!');
});
