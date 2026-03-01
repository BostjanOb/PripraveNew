<?php

new class extends Livewire\Component
{
    public function unlink(string $provider): void
    {
        $this->validateProvider($provider);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (! $user->getAuthPassword()) {
            $this->addError('unlink', 'Računa ni mogoče prekiniti brez nastavljenega gesla.');

            return;
        }

        $column = match ($provider) {
            'google' => 'google_id',
            'facebook' => 'facebook_id',
            default => null,
        };

        if (! $column) {
            return;
        }

        $user->forceFill([$column => null])->save();
    }

    private function validateProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 422);
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $this->view([
            'hasGoogle' => (bool) $user->google_id,
            'hasFacebook' => (bool) $user->facebook_id,
            'canUnlink' => (bool) $user->getAuthPassword(),
        ]);
    }
}
?>
<div class="rounded-2xl border border-border bg-card p-6 md:p-8">
    <div class="mb-5 flex items-center gap-2">
        <div class="flex size-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/50">
            <x-icon-regular.link class="size-4 text-purple-600 dark:text-purple-400" />
        </div>
        <h2 class="font-serif text-lg font-bold text-foreground">Povezani računi</h2>
    </div>

    <div class="space-y-4">
        <p class="text-sm text-muted-foreground">
            Povežite svoj račun z družbenimi omrežji za hitrejšo prijavo.
        </p>

        @error('unlink')
            <flux:callout variant="danger" icon="icon-regular.triangle-exclamation">
                <flux:callout.text>{{ $message }}</flux:callout.text>
            </flux:callout>
        @enderror

        {{-- Facebook --}}
        <div class="flex items-center justify-between rounded-xl border border-border bg-muted/30 p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-600">
                    <x-icon-brands.facebook class="size-6 text-white" />
                </div>
                <div>
                    <div class="font-semibold text-foreground">Facebook</div>
                    <div class="text-xs text-muted-foreground">
                        {{ $hasFacebook ? 'Povezano' : 'Ni povezano' }}
                    </div>
                </div>
            </div>
            @if($hasFacebook)
                @if($canUnlink)
                    <flux:button wire:click="unlink('facebook')"
                                wire:confirm="Ali res želite prekiniti povezavo s Facebook?"
                                variant="outline"
                                size="sm"
                                icon="icon-regular.link-broken"
                                class="gap-1.5 border-blue-200 text-blue-600 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-950/50">
                        Prekini povezavo
                    </flux:button>
                @else
                    <flux:tooltip content="Najprej nastavite geslo, da boste lahko prekinili to povezavo.">
                        <flux:badge color="blue" icon="icon-regular.link">Povezano</flux:badge>
                    </flux:tooltip>
                @endif
            @else
                <flux:button href="{{ route('social.redirect', 'facebook') }}" size="sm" icon="icon-regular.link"
                             class="gap-1.5 bg-blue-600 text-white hover:bg-blue-700">
                    Poveži
                </flux:button>
            @endif
        </div>

        {{-- Google --}}
        <div class="flex items-center justify-between rounded-xl border border-border bg-muted/30 p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-white shadow-sm dark:bg-zinc-800">
                    <x-icon-custom.google class="size-5" />
                </div>
                <div>
                    <div class="font-semibold text-foreground">Google</div>
                    <div class="text-xs text-muted-foreground">
                        {{ $hasGoogle ? 'Povezano' : 'Ni povezano' }}
                    </div>
                </div>
            </div>
            @if($hasGoogle)
                @if($canUnlink)
                    <flux:button wire:click="unlink('google')"
                                wire:confirm="Ali res želite prekiniti povezavo z Google?"
                                variant="outline" size="sm" icon="icon-regular.link-broken"
                                class="gap-1.5 border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-900/50">
                        Prekini povezavo
                    </flux:button>
                @else
                    <flux:tooltip content="Najprej nastavite geslo, da boste lahko prekinili to povezavo.">
                        <flux:badge color="zinc" icon="icon-regular.link">Povezano</flux:badge>
                    </flux:tooltip>
                @endif
            @else
                <flux:button href="{{ route('social.redirect', 'google') }}" size="sm" icon="icon-regular.link"
                             class="gap-1.5 bg-gray-800 text-white hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600">
                    Poveži
                </flux:button>
            @endif
        </div>

        @if(! $canUnlink && ($hasGoogle || $hasFacebook))
            <p class="text-xs text-muted-foreground">
                Ker ste se prijavili prek družbenega omrežja brez gesla, najprej
                <a href="{{ route('password.request') }}" class="font-medium text-foreground underline underline-offset-2 hover:no-underline">nastavite geslo</a>,
                da boste lahko prekinili katerokoli povezavo.
            </p>
        @endif
    </div>
</div>
