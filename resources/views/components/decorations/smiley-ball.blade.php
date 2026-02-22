@props(['class' => ''])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 48 48"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <circle cx="24" cy="24" r="22" fill="#F4D03F" />
    <circle cx="24" cy="24" r="22" stroke="#E6C12E" stroke-width="1.5" />
    <circle cx="16" cy="20" r="3" fill="#333" />
    <circle cx="32" cy="20" r="3" fill="#333" />
    <circle cx="17" cy="19" r="1" fill="#fff" />
    <circle cx="33" cy="19" r="1" fill="#fff" />
    <path d="M14 28 Q24 38 34 28" stroke="#333" stroke-width="2.5" stroke-linecap="round" fill="none" />
</svg>
