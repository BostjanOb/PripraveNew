@props(['class' => ''])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 200 200"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <circle cx="30" cy="40" r="4" fill="#E8943A" opacity="0.3" />
    <circle cx="170" cy="30" r="3" fill="#5B8DB8" opacity="0.25" />
    <circle cx="60" cy="160" r="5" fill="#3B9B7A" opacity="0.2" />
    <circle cx="150" cy="140" r="3" fill="#E85D5D" opacity="0.25" />
    <circle cx="100" cy="20" r="3" fill="#9B59B6" opacity="0.2" />
    <rect x="40" y="90" width="6" height="6" rx="1" fill="#F4D03F" opacity="0.3" transform="rotate(30 43 93)" />
    <rect x="140" y="70" width="5" height="5" rx="1" fill="#E8943A" opacity="0.25" transform="rotate(45 142 72)" />
    <rect x="80" y="170" width="6" height="6" rx="1" fill="#5B8DB8" opacity="0.2" transform="rotate(15 83 173)" />
    <polygon points="120,50 126,60 114,60" fill="#3B9B7A" opacity="0.2" />
    <polygon points="20,130 26,140 14,140" fill="#E85D5D" opacity="0.2" />
    <polygon points="180,170 186,180 174,180" fill="#F4D03F" opacity="0.25" />
</svg>
