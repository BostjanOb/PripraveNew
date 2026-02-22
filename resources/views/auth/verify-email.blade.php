<x-layouts.app title="Potrdite e-pošto — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 text-center shadow-sm">

            <div class="mb-6 flex flex-col items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                    {{-- Mail icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-primary">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-foreground">Preverite svojo e-pošto</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Na vaš e-poštni naslov smo poslali potrditveno povezavo. Kliknite nanjo, da potrdite svoj račun.
                    </p>
                </div>
            </div>

            {{-- Email display --}}
            <div class="mb-6 rounded-lg border border-border bg-secondary/50 px-4 py-3">
                <p class="text-xs text-muted-foreground">Poslano na</p>
                <p class="font-medium text-foreground">{{ auth()->user()->email }}</p>
            </div>

            {{-- Session status --}}
            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 rounded-md bg-primary/10 px-4 py-3 text-sm text-primary">
                    Novo potrditveno sporočilo je bilo poslano na vaš e-poštni naslov.
                </div>
            @endif

            <div class="space-y-3">
                <p class="text-sm text-muted-foreground">Niste prejeli e-pošte?</p>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-md border border-border bg-background px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-secondary">
                        Ponovno pošlji potrditveni e-mail
                    </button>
                </form>
            </div>

            <div class="mt-6">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Nazaj na prijavo
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
