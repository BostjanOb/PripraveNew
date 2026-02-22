<x-layouts.app title="Uredi profil" mainClass="flex-1">
    <div class="relative">

        {{-- ── Hero ── --}}
        <div class="relative overflow-hidden border-b border-border bg-card">
            <svg class="pointer-events-none absolute -left-6 -top-2 size-48 opacity-30" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/><circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/><circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/><circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/><circle cx="180" cy="30" r="4" fill="#0ea5e9" opacity=".6"/><circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/><circle cx="80" cy="55" r="4" fill="#10b981" opacity=".4"/><circle cx="125" cy="65" r="3" fill="#f59e0b" opacity=".5"/><circle cx="165" cy="50" r="5" fill="#6366f1" opacity=".4"/><circle cx="15" cy="100" r="4" fill="#ec4899" opacity=".5"/>
            </svg>
            <svg class="pointer-events-none absolute -right-4 bottom-0 size-36 rotate-180 opacity-20" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/><circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/><circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/><circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/><circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/>
            </svg>

            <div class="relative mx-auto max-w-4xl px-4 py-8 text-center md:py-10">
                <a href="{{ route('profile') }}"
                   class="mb-6 inline-flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Nazaj na profil
                </a>
                <h1 class="font-serif text-3xl font-bold text-foreground md:text-4xl">Uredi profil</h1>
                <p class="mt-3 text-base text-muted-foreground md:text-lg">
                    Posodobite svoje podatke in nastavitve profila.
                </p>
            </div>
        </div>

        {{-- ── Form sections ── --}}
        <div class="mx-auto max-w-4xl space-y-8 px-4 py-8 md:py-12">
            <livewire:profile.avatar-upload />
            <livewire:profile.basic-info-form />
            <livewire:profile.change-password-form />
            <livewire:profile.linked-accounts />
        </div>

    </div>
</x-layouts.app>
