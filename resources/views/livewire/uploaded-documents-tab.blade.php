<div>
    {{-- Search bar --}}
    <div class="mb-4 flex items-center gap-3">
        <div class="relative flex-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input wire:model.live.debounce.300ms="search"
                   type="search"
                   placeholder="Iskanje po naslovu..."
                   class="w-full rounded-xl border border-border bg-background py-2.5 pl-10 pr-4 text-sm text-foreground placeholder-muted-foreground outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20" />
        </div>
    </div>

    {{-- Results --}}
    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card py-16 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-3 size-10 text-muted-foreground/50">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            @if($search)
                <p class="text-sm font-medium text-foreground">Ni rezultatov za "{{ $search }}"</p>
                <p class="mt-1 text-xs text-muted-foreground">Poskusite z drugim iskanjem.</p>
            @else
                <p class="text-sm font-medium text-foreground">Še niste naložili nobene priprave</p>
                <p class="mt-1 text-xs text-muted-foreground">Dodajte svojo prvo pripravo in delajte z drugimi učitelji.</p>
            @endif
        </div>
    @else
        <div class="space-y-3">
            @foreach($documents as $document)
                <x-document-row :document="$document" :showActions="true" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif
</div>
