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
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                    </svg>
                    Google
                </flux:button>
                <flux:button href="{{ url('/auth/facebook/redirect') }}" variant="outline" class="w-full">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
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
