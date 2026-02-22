@blaze(fold: true, memo: true)
@props([
    'icon' => null,
    'name' => null,
    'variant' => 'outline',
])

@php
if ($icon === 'loading') {
    $icon = 'icon-regular.user';
}

$classes = Flux::classes('shrink-0')
    ->add(match($variant) {
        'outline' => '[:where(&)]:size-5',
        'solid' => '[:where(&)]:size-5',
        'mini' => '[:where(&)]:size-4',
        'micro' => '[:where(&)]:size-3.5',
    });
@endphp

<x-dynamic-component :component="$icon" {{ $attributes->class($classes) }} />