<x-layouts.app title="Pozabljeno geslo — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            {{-- Header --}}
            <div class="mb-6 flex flex-col items-center gap-3 text-center">
                <div class="flex size-14 items-center justify-center rounded-full bg-primary/10">
                    <x-icon-regular.envelope class="size-6 text-primary" />
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-foreground">Pozabljeno geslo</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Vnesite svoj e-poštni naslov in poslali vam bomo povezavo za ponastavitev gesla.</p>
                </div>
            </div>

            {{-- Session status --}}
            @if (session('status'))
                <div class="mb-4 rounded-md bg-primary/10 px-4 py-3 text-sm text-primary">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <flux:field>
                    <flux:label for="email">E-poštni naslov</flux:label>
                    <flux:input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="ime@primer.si"
                        autocomplete="email"
                        required
                        autofocus
                        :invalid="$errors->has('email')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="email" />
                </flux:field>

                <flux:button type="submit" variant="primary" class="w-full">
                    Pošlji povezavo za ponastavitev
                </flux:button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                    <x-icon-regular.arrow-left class="size-3.5" />
                    Nazaj na prijavo
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
