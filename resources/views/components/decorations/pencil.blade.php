@props(['class' => ''])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 16 80"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <rect x="2" y="10" width="12" height="50" rx="1" fill="#F4D03F" />
    <rect x="2" y="10" width="4" height="50" fill="#E6C12E" />
    <rect x="1" y="56" width="14" height="8" rx="1" fill="#C0C0C0" />
    <rect x="1" y="58" width="14" height="1.5" fill="#A8A8A8" />
    <rect x="1" y="61" width="14" height="1.5" fill="#A8A8A8" />
    <rect x="2" y="64" width="12" height="8" rx="2" fill="#E8839B" />
    <polygon points="2,10 14,10 8,0" fill="#F5DEB3" />
    <polygon points="6,4 10,4 8,0" fill="#333333" />
</svg>
