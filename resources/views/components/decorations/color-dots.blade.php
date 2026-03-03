@props(['class' => ''])

<svg
    {{ $attributes->merge(['class' => 'pointer-events-none ' . $class]) }}
    viewBox="0 0 200 200"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
>
    <circle cx="20" cy="20" r="4" fill="#10b981" opacity=".6"/>
    <circle cx="60" cy="15" r="3" fill="#f59e0b" opacity=".5"/>
    <circle cx="100" cy="25" r="5" fill="#6366f1" opacity=".4"/>
    <circle cx="140" cy="10" r="3" fill="#ec4899" opacity=".5"/>
    <circle cx="180" cy="30" r="4" fill="#0ea5e9" opacity=".6"/>
    <circle cx="35" cy="60" r="3" fill="#a855f7" opacity=".5"/>
    <circle cx="80" cy="55" r="4" fill="#10b981" opacity=".4"/>
    <circle cx="125" cy="65" r="3" fill="#f59e0b" opacity=".5"/>
    <circle cx="165" cy="50" r="5" fill="#6366f1" opacity=".4"/>
    <circle cx="15" cy="100" r="4" fill="#ec4899" opacity=".5"/>
</svg>
