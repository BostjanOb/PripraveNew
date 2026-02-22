<x-layouts.app title="Ponastavitev gesla — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            {{-- Header --}}
            <div class="mb-6 flex flex-col items-center gap-3 text-center">
                <div class="flex size-14 items-center justify-center rounded-full bg-primary/10">
                    <x-icon-regular.key class="size-6 text-primary" />
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-foreground">Ponastavitev gesla</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Vnesite novo geslo za svoj račun.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                <flux:field>
                    <flux:label for="password">Novo geslo</flux:label>
                    <flux:input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Najmanj 8 znakov"
                        autocomplete="new-password"
                        required
                        autofocus
                        viewable
                        :invalid="$errors->has('password')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label for="password_confirmation">Ponovite novo geslo</flux:label>
                    <flux:input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="Ponovite geslo"
                        autocomplete="new-password"
                        required
                        viewable
                        class="w-full"
                        class:input="text-sm"
                    />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full">
                    Ponastavi geslo
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.app>
