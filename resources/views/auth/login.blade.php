<x-layouts.app title="Prijava — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            <div class="mb-6 flex flex-col items-center text-center">
                <h1 class="font-serif text-2xl font-bold text-foreground">Prijava</h1>
                <p class="mt-1 text-sm text-muted-foreground">Prijavite se v svoj račun za dostop do učnih priprav</p>
            </div>

            @if (session('status'))
                <div class="mb-4 rounded-md bg-primary/10 px-4 py-3 text-sm text-primary">
                    {{ session('status') }}
                </div>
            @endif

            {{-- OAuth buttons --}}
            <div class="grid grid-cols-2 gap-3">
                <flux:button href="{{ url('/auth/google/redirect') }}" variant="outline" class="w-full">
                    <x-icon-custom.google class="size-4" />
                    Google
                </flux:button>
                <flux:button href="{{ url('/auth/facebook/redirect') }}" variant="outline" class="w-full">
                    <x-icon-brands.facebook class="size-4" style="color: #1877F2" />
                    Facebook
                </flux:button>
            </div>

            {{-- Separator --}}
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-border"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-card px-3 text-xs text-muted-foreground">ali</span>
                </div>
            </div>

            {{-- Login form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                        tabindex="1"
                        :invalid="$errors->has('email')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="email" />
                </flux:field>

                <flux:field class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <flux:label for="password">Geslo</flux:label>
                        <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline">
                            Pozabljeno geslo?
                        </a>
                    </div>
                    <flux:input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Vnesite geslo"
                        autocomplete="current-password"
                        required
                        tabindex="2"
                        viewable
                        :invalid="$errors->has('password')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:checkbox
                    id="remember"
                    name="remember"
                    value="1"
                    label="Zapomni si me"
                    :checked="(bool) old('remember')"
                />

                <flux:button type="submit" variant="primary" tabindex="3" class="w-full">
                    Prijava
                </flux:button>
            </form>

            <p class="mt-4 text-center text-sm text-muted-foreground">
                Se nimate računa?
                <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">Registrirajte se</a>
            </p>
        </div>
    </div>
</x-layouts.app>
