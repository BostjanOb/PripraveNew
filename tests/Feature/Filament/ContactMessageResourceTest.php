<?php

use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('loads the contact messages resource page', function () {
    $messages = ContactMessage::factory()->count(3)->create();

    Livewire::test(ManageContactMessages::class)
        ->assertOk()
        ->assertCanSeeTableRecords($messages);
});

it('displays messages sorted by newest first', function () {
    $older = ContactMessage::factory()->create(['created_at' => now()->subDay()]);
    $newer = ContactMessage::factory()->create(['created_at' => now()]);

    Livewire::test(ManageContactMessages::class)
        ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
});

it('can search messages by name', function () {
    $match = ContactMessage::factory()->create(['name' => 'Ana Novak']);
    $noMatch = ContactMessage::factory()->create(['name' => 'Janez Kovač']);

    Livewire::test(ManageContactMessages::class)
        ->searchTable('Ana Novak')
        ->assertCanSeeTableRecords([$match])
        ->assertCanNotSeeTableRecords([$noMatch]);
});

it('replies to a contact message via modal action', function () {
    Mail::fake();

    $message = ContactMessage::factory()->create();

    Livewire::test(ManageContactMessages::class)
        ->callAction(TestAction::make('reply')->table($message), data: [
            'reply_message' => 'Hvala za vaše sporočilo. Tu je naš odgovor.',
        ])
        ->assertNotified();

    $message->refresh();

    expect($message->reply_message)->toBe('Hvala za vaše sporočilo. Tu je naš odgovor.');
    expect($message->replied_at)->not->toBeNull();
    expect($message->replied_by)->not->toBeNull();

    Mail::assertQueued(ContactMessageReplyMail::class, function (ContactMessageReplyMail $mail) use ($message): bool {
        return $mail->hasTo($message->email);
    });
});

it('hides reply action for already replied messages', function () {
    $replied = ContactMessage::factory()->replied()->create();

    Livewire::test(ManageContactMessages::class)
        ->assertActionHidden(TestAction::make('reply')->table($replied));
});
