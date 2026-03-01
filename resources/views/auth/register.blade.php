<x-layouts.app title="Registracija — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            {{-- Logo --}}
            <div class="mb-6 flex flex-col items-center text-center">
                <h1 class="font-serif text-2xl font-bold text-foreground">Registracija</h1>
                <p class="mt-1 text-sm text-muted-foreground">Ustvarite račun in začnite deliti učne priprave</p>
            </div>

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

            {{-- Register form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <flux:field>
                    <flux:label for="display_name">Prikazno ime</flux:label>
                    <flux:input
                        id="display_name"
                        name="display_name"
                        type="text"
                        value="{{ old('display_name') }}"
                        placeholder="Janez Novak"
                        autocomplete="name"
                        required
                        autofocus
                        :invalid="$errors->has('display_name')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="display_name" />
                </flux:field>

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
                        :invalid="$errors->has('email')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label for="password">Geslo</flux:label>
                    <flux:input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Najmanj 8 znakov"
                        autocomplete="new-password"
                        required
                        viewable
                        :invalid="$errors->has('password')"
                        class="w-full"
                        class:input="text-sm"
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label for="password_confirmation">Ponovite geslo</flux:label>
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

                <div class="flex items-start gap-2">
                    <flux:checkbox id="terms" name="terms" required class="mt-0.5" />
                    <label for="terms" class="text-sm font-normal leading-snug text-muted-foreground">
                        Strinjam se s
                        <a href="{{ url('/pogoji-uporabe') }}" class="text-primary hover:underline">pogoji uporabe</a>
                        in
                        <a href="{{ url('/zasebnost') }}" class="text-primary hover:underline">politiko zasebnosti</a>
                    </label>
                </div>
                <flux:error name="terms" />

                <flux:button type="submit" variant="primary" class="w-full">
                    Registracija
                </flux:button>
            </form>

            <p class="mt-4 text-center text-sm text-muted-foreground">
                Že imate račun?
                <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Prijavite se</a>
            </p>
        </div>
    </div>
</x-layouts.app>
