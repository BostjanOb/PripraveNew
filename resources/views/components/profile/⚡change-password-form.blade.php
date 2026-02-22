<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

new class extends Livewire\Component
{
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $confirmPassword = '';

    public bool $passwordChanged = false;

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', Password::defaults(), 'different:currentPassword'],
            'confirmPassword' => ['required', 'string', 'same:newPassword'],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Trenutno geslo ni pravilno.');

            return;
        }

        $user->forceFill([
            'password' => Hash::make($this->newPassword),
        ])->save();

        $this->currentPassword = '';
        $this->newPassword = '';
        $this->confirmPassword = '';
        $this->passwordChanged = true;
    }
}
?>
<div class="rounded-2xl border border-border bg-card p-6 md:p-8">
    <div class="mb-5 flex items-center gap-2">
        <div class="flex size-8 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900/50">
            <x-icon-regular.shield-check class="size-4 text-rose-600 dark:text-rose-400" />
        </div>
        <h2 class="font-serif text-lg font-bold text-foreground">Varnost</h2>
    </div>

    <div class="space-y-5">
        <flux:field>
            <flux:label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                <x-icon-regular.lock class="size-3.5" />
                Trenutno geslo
            </flux:label>
            <flux:input wire:model="currentPassword" type="password" placeholder="Vnesite trenutno geslo" viewable />
            <flux:error name="currentPassword" />
        </flux:field>

        <flux:field>
            <flux:label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                <x-icon-regular.lock class="size-3.5" />
                Novo geslo
            </flux:label>
            <flux:input wire:model="newPassword" type="password" placeholder="Vnesite novo geslo" viewable />
            <flux:error name="newPassword" />
        </flux:field>

        <flux:field>
            <flux:label class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                <x-icon-regular.lock class="size-3.5" />
                Potrdite novo geslo
            </flux:label>
            <flux:input wire:model="confirmPassword" type="password" placeholder="Ponovite novo geslo" viewable />
            <flux:error name="confirmPassword" />
        </flux:field>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <flux:button wire:click="changePassword"
                         variant="outline"
                         size="sm"
                         icon="icon-regular.lock"
                         class="h-11! gap-1.5 transition-colors border-rose-200 text-rose-600! hover:border-rose-300! hover:bg-rose-50! hover:text-rose-700! dark:border-rose-800! dark:text-rose-400! dark:hover:bg-rose-950/50!">
                Spremeni geslo
            </flux:button>

            @if($passwordChanged)
                <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400"
                     x-data x-init="setTimeout(() => $el.remove(), 3000)">
                    <x-icon-regular.check-circle class="size-4" />
                    <span class="font-medium">Geslo uspešno spremenjeno!</span>
                </div>
            @endif
        </div>
    </div>
</div>
