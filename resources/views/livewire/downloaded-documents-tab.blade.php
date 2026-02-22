<div>
    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card py-16 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-3 size-10 text-muted-foreground/50">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <p class="text-sm font-medium text-foreground">Še niste prenesli nobene priprave</p>
            <p class="mt-1 text-xs text-muted-foreground">Brskajte po prijavah in prenesite tiste, ki vam koristijo.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($documents as $document)
                <x-document-row :document="$document" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif
</div>
