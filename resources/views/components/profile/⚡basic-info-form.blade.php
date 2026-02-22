<?php

new class extends Livewire\Component
{
    public string $displayName = '';

    public string $name = '';

    public string $email = '';

    public bool $basicInfoSaved = false;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $this->displayName = $user->display_name;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function saveBasicInfo(): void
    {
        $this->validate([
            'displayName' => ['required', 'string', 'min:2', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->forceFill([
            'display_name' => $this->displayName,
            'name' => $this->name,
        ])->save();

        $this->basicInfoSaved = true;
        $this->dispatch('basic-info-saved');
    }
}

?>

<div class="rounded-2xl border border-border bg-card p-6 md:p-8">
    <div class="mb-5 flex items-center gap-2">
        <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/50">
            <x-icon-regular.user class="size-4 text-amber-600 dark:text-amber-400" />
        </div>
        <h2 class="font-serif text-lg font-bold text-foreground">Osnovni podatki</h2>
    </div>

    <form wire:submit="saveBasicInfo" class="space-y-5">
        <flux:field>
            <flux:label class="flex items-center gap-1.5 text-xs font-bold! uppercase tracking-wider text-amber-600 dark:text-amber-400">
                <x-icon-regular.comment-alt-captions class="size-3.5" />
                Prikazno ime
            </flux:label>
            <flux:input wire:model="displayName" type="text" placeholder="Vaše prikazno ime" />
            <flux:error name="displayName" />
        </flux:field>

        <flux:field>
            <flux:label class="flex items-center gap-1.5 text-xs font-bold! uppercase tracking-wider text-amber-600 dark:text-amber-400">
                <x-icon-regular.user class="size-3.5" />
                Ime in priimek
            </flux:label>
            <flux:input wire:model="name" type="text" placeholder="Vaše ime in priimek" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label class="flex items-center gap-1.5 text-xs font-bold! uppercase tracking-wider text-amber-600 dark:text-amber-400">
                <x-icon-regular.envelope class="size-3.5" />
                E-poštni naslov
            </flux:label>
            <flux:input :value="$email" type="email" disabled />
            <flux:description>E-poštni naslov ni mogoče spremeniti.</flux:description>
        </flux:field>

        <div class="flex flex-wrap items-center gap-3 pt-3">
            <flux:button type="submit" icon="icon-regular.save" 
                class="h-11! text-base!"
                variant="primary">
                Shrani spremembe
            </flux:button>

            @if($basicInfoSaved)
                <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400"
                     x-data x-init="setTimeout(() => $el.remove(), 3000)">
                    <x-icon-regular.check-circle class="size-4" />
                    <span class="font-medium">Uspešno shranjeno!</span>
                </div>
            @endif
        </div>
    </form>
</div>
