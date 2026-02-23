@props([
    'badge', // array from BadgeRegistry
])

<span
    title="{{ $badge['name'] }} — {{ $badge['description'] }}"
    class="inline-flex size-4 items-center justify-center rounded border {{ $badge['color']['bg'] }} {{ $badge['color']['border'] }}"
>
    <x-badge-svg :badgeId="$badge['id']" class="size-3 {{ $badge['color']['text'] }}" />
</span>
