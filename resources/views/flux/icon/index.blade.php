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
        'mini' => '[:where(&)]:size-4',
        'micro' => '[:where(&)]:size-3.5',
    });
@endphp

@if($icon === 'loading')
    {{-- Spinner for loading state --}}
    <svg {{ $attributes->class([$classes, 'animate-spin']) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
@elseif($icon && str_starts_with($icon, 'icon-'))
    {{-- Custom application icons (e.g. icon-regular.user) --}}
    <x-dynamic-component :component="$icon" {{ $attributes->class($classes) }} />
@elseif($icon)
    {{-- Built-in Flux/Heroicons (e.g. exclamation-triangle) --}}
    <flux:delegate-component :component="'icon.' . $icon">{{ $slot }}</flux:delegate-component>
@endif