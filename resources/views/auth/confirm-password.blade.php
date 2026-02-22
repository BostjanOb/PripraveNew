<x-layouts.app title="Potrdite geslo — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 shadow-sm">

            {{-- Header --}}
            <div class="mb-6 flex flex-col items-center gap-3 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
                    {{-- Lock icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-foreground">Potrdite geslo</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Pred nadaljevanjem potrdite geslo.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-foreground">Geslo</label>
                    <div x-data="{ show: false }" class="relative">
                        <input
                            id="password"
                            name="password"
                            :type="show ? 'text' : 'password'"
                            placeholder="Vnesite geslo"
                            autocomplete="current-password"
                            required
                            autofocus
                            class="w-full rounded-md border border-input bg-background px-3 py-2 pr-10 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1 @error('password') border-destructive @enderror"
                        >
                        <button type="button" @click="show = !show" :aria-label="show ? 'Skrij geslo' : 'Pokaži geslo'" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <template x-if="show">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </template>
                            <template x-if="!show">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
                    Potrdi
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
