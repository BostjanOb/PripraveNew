<x-layouts.app title="Potrdite e-pošto — {{ config('app.name') }}">
    <div class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-xl border border-border bg-card p-8 text-center shadow-sm">

            <div class="mb-6 flex flex-col items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                    <x-icon-regular.envelope class="size-8 text-primary"/>
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
                    <flux:button type="submit" variant="outline" class="w-full">
                        Ponovno pošlji potrditveni e-mail
                    </flux:button>
                </form>
            </div>

            <div class="mt-6">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                    <x-icon-regular.arrow-left class="size-3.5"/>
                    Nazaj na prijavo
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
