<div>
    {{-- Results --}}
    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card py-16 text-center">
            <x-icon-regular.file-lines  class="mb-3 size-12 text-muted-foreground/50"/>
            <p class="text-sm font-medium text-foreground">Ta uporabnik še nima naloženih priprav</p>
            <p class="mt-1 text-xs text-muted-foreground">Zaenkrat tukaj še ni ničesar.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($documents as $document)
                <x-document-row :$document wire:key="doc-{{ $document->id }}" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif
</div>
