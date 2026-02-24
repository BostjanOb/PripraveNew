<?php

use App\Events\ContactMessageSubmitted;
use App\Models\ContactMessage;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public bool $isSubmitted = false;

    public function submit(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create($validated);

        ContactMessageSubmitted::dispatch($contactMessage);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->isSubmitted = true;
    }
};
?>

<div>
    @if ($isSubmitted)
        <div class="rounded-2xl border-2 border-teal-200 bg-teal-50 p-8 text-center dark:border-teal-400/35 dark:bg-slate-900">
            <div class="mx-auto mb-3 flex size-12 items-center justify-center rounded-full bg-teal-100 dark:bg-teal-900/50">
                <x-icon-regular.check-circle class="size-6 text-teal-600 dark:text-teal-400" />
            </div>
            <h3 class="font-serif text-lg font-bold text-foreground">
                Sporočilo poslano!
            </h3>
            <p class="mt-2 text-sm text-muted-foreground">
                Hvala za vaše sporočilo. Odgovorili vam bomo v najkrajšem možnem času.
            </p>
            <button
                wire:click="$set('isSubmitted', false)"
                class="cursor-pointer mt-4 text-sm font-semibold text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300"
            >
                Pošlji novo sporočilo
            </button>
        </div>
    @else
        <div class="rounded-2xl border border-border bg-card p-6 md:p-8">
            <h2 class="font-serif text-lg font-bold text-foreground">
                Pošlji sporočilo
            </h2>
            <p class="mt-1 text-sm text-muted-foreground">
                Izpolnite obrazec in odgovorili vam bomo v najkrajšem možnem času.
            </p>

            <form wire:submit="submit" class="mt-6 space-y-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>Ime</flux:label>
                        <flux:input wire:model="name" placeholder="Vaše ime" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>E-pošta</flux:label>
                        <flux:input type="email" wire:model="email" placeholder="ime@primer.si" />
                        <flux:error name="email" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Zadeva</flux:label>
                    <flux:input wire:model="subject" placeholder="O čem bi radi pisali?" />
                    <flux:error name="subject" />
                </flux:field>

                <flux:field>
                    <flux:label>Sporočilo</flux:label>
                    <flux:textarea wire:model="message" rows="5" placeholder="Vaše sporočilo..." />
                    <flux:error name="message" />
                </flux:field>

                <flux:button type="submit" variant="primary" 
                    icon="icon-regular.paper-plane"
                    class="w-full sm:w-auto cursor-pointer">
                    Pošlji sporočilo
                </flux:button>
            </form>
        </div>
    @endif
</div>
