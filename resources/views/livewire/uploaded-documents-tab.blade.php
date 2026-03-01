<div>
    {{-- Search bar --}}
    <div class="mb-4 flex items-center gap-3">
        <div class="relative flex-1">
            <x-icon-regular.magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"/>
            <input wire:model.live.debounce.300ms="search"
                   type="search"
                   placeholder="Iskanje po naslovu..."
                   class="w-full rounded-xl border border-border bg-background py-2.5 pl-10 pr-4 text-sm text-foreground placeholder-muted-foreground outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20" />
        </div>
    </div>

    {{-- Results --}}
    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card py-16 text-center">
            <x-icon-regular.file-lines class="mb-3 size-10 text-muted-foreground/50"/>
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
                <x-document-row :$document :showActions="true" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif
</div>
