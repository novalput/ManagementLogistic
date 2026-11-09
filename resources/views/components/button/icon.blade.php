@props(['color' => 'gray'])

@php
$colors = [
    'gray' => 'text-gray-400 hover:text-gray-600',
    'red' => 'text-gray-400 hover:text-red-600',
    'orange' => 'text-gray-400 hover:text-[#E2852E]',
][$color] ?? 'text-gray-400 hover:text-gray-600';
@endphp

<button {{ $attributes->merge(['class' => "p-1 $colors transition-colors rounded"]) }}>
    {{ $slot }}
</button>
