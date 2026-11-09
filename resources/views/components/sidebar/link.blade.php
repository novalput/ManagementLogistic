@props(['route', 'label', 'active' => false])

@php
    $classes = $active
        ? 'flex items-center gap-4 text-[#0D1A63] hover:text-[#1b2f8a] transition-colors group sidebar-active'
        : 'flex items-center gap-4 text-gray-500 hover:text-[#0D1A63] transition-colors group';
@endphp

<a href="{{ $route }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
    <span class="text-sm font-medium">{{ $label }}</span>
</a>
