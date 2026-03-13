@props([
    'badge', // App\Enums\Badge
])

@php
    $color = $badge->color();
@endphp

<span
    title="{{ $badge->label() }} — {{ $badge->description() }}"
    class="inline-flex size-4 items-center justify-center rounded border {{ $color['bg'] }} {{ $color['border'] }}"
>
    <x-dynamic-component :component="$badge->icon()" class="size-3 {{ $color['text'] }}" />
</span>
