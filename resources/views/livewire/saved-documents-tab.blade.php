<div>
    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-card py-16 text-center">
            <x-icon-regular.heart  class="mb-3 size-10 text-muted-foreground/50"/>
            <p class="text-sm font-medium text-foreground">Še niste shranili nobene priprave</p>
            <p class="mt-1 text-xs text-muted-foreground">Shranite priprave, ki jih želite hitro najti pozneje.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($documents as $document)
                <x-document-row :$document wire:key="saved-document-{{ $document->id }}" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif
</div>
