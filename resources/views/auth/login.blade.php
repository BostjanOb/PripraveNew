<x-layouts.app title="Prijava — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            {{-- Logo --}}
            <div class="mb-6 flex flex-col items-center gap-3 text-center">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <div class="relative flex h-9 w-9 items-center justify-center rounded-lg bg-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-primary-foreground">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute -right-1 -top-1 h-3.5 w-3.5 rotate-45 text-accent">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </div>
                    <span class="font-serif text-lg font-bold tracking-tight text-foreground">
                        Priprave<span class="text-primary">.net</span>
                    </span>
                </a>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-foreground">Prijava</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Prijavite se v svoj račun za dostop do učnih priprav</p>
                </div>
            </div>

            {{-- Session status --}}
            @if (session('status'))
                <div class="mb-4 rounded-md bg-primary/10 px-4 py-3 text-sm text-primary">
                    {{ session('status') }}
                </div>
            @endif

            {{-- OAuth buttons --}}
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ url('/auth/google/redirect') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4" />
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                    </svg>
                    Google
                </a>
                <a href="{{ url('/auth/facebook/redirect') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-md border border-border bg-background px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="#1877F2">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    Facebook
                </a>
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

                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-foreground">E-poštni naslov</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="ime@primer.si"
                        autocomplete="email"
                        required
                        autofocus
                        tabindex="1"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1 @error('email') border-destructive @enderror"
                    >
                    @error('email')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-sm font-medium text-foreground">Geslo</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline">
                            Pozabljeno geslo?
                        </a>
                    </div>
                    <div x-data="{ show: false }" class="relative">
                        <input
                            id="password"
                            name="password"
                            :type="show ? 'text' : 'password'"
                            placeholder="Vnesite geslo"
                            autocomplete="current-password"
                            required
                            tabindex="2"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 pr-10 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1 @error('password') border-destructive @enderror"
                        >
                        <button
                            type="button"
                            tabindex="-1"
                            @click="show = !show"
                            :aria-label="show ? 'Skrij geslo' : 'Pokaži geslo'"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <template x-if="show">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </template>
                            <template x-if="!show">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                    tabindex="3"
                    class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                    Prijava
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-muted-foreground">
                Se nimate računa?
                <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">Registrirajte se</a>
            </p>
        </div>
    </div>
</x-layouts.app>
