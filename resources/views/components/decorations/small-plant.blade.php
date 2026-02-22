@props(['class' => ''])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 60 70"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <path d="M15 45 L45 45 L40 65 L20 65 Z" fill="#D4832F" />
    <rect x="12" y="42" width="36" height="6" rx="2" fill="#E8943A" />
    <path d="M30 42 C30 30 30 25 30 20" stroke="#3B9B7A" stroke-width="2.5" stroke-linecap="round" />
    <ellipse cx="22" cy="28" rx="10" ry="6" transform="rotate(-30 22 28)" fill="#4CAF7A" />
    <ellipse cx="38" cy="24" rx="10" ry="6" transform="rotate(25 38 24)" fill="#3B9B7A" />
    <ellipse cx="26" cy="16" rx="8" ry="5" transform="rotate(-15 26 16)" fill="#5BBF8A" />
</svg>
