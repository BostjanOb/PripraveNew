<x-layouts.app title="Potrdite geslo — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            {{-- Header --}}
            <div class="mb-6 flex flex-col items-center gap-3 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
                    <x-icon-regular.lock class="h-7 w-7 text-primary"/>
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-foreground">Potrdite geslo</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Pred nadaljevanjem potrdite geslo.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <flux:field>
                    <flux:label for="password">Geslo</flux:label>
                    <flux:input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Vnesite geslo"
                        autocomplete="current-password"
                        required
                        autofocus
                        viewable
                        :invalid="$errors->has('password')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full">
                    Potrdi
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.app>
