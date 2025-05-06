@props(['active'])

@php
    $classes = ($active ?? false)
                ? 'block pl-3 pr-4 py-2 border-l-4 border-green-500 text-base font-medium text-green-300 bg-[#2d2d44] focus:outline-none focus:text-green-400 focus:bg-[#2d2d44] transition'
                : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-green-300 hover:bg-[#2d2d44] transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>