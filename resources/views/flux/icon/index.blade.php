@blaze(fold: true, memo: true)
@props([
    'icon' => null,
    'name' => null,
    'variant' => 'outline',
])

@php
$icon = $name ?? $icon;

$classes = Flux::classes('shrink-0')
    ->add(match($variant) {
        'outline' => '[:where(&)]:size-5',
        'solid' => '[:where(&)]:size-5',
        'mini' => '[:where(&)]:size-5',
        'micro' => '[:where(&)]:size-4',
    });
@endphp

@if($icon && str_starts_with($icon, 'icon-'))
    {{-- Custom application icons (e.g. icon-regular.user) --}}
    <x-dynamic-component :component="$icon" {{ $attributes->class($classes) }} />
@else
    {{-- Built-in Flux/Heroicons (e.g. exclamation-triangle) --}}
    <flux:delegate-component :component="'icon.' . $icon">{{ $slot }}</flux:delegate-component>
@endif