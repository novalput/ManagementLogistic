@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all shadow-sm']) !!}>
    {{ $slot }}
</select>
