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
                    <svg class="size-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
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
                    <svg class="size-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
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
