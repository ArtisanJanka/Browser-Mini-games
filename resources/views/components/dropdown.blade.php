@props(['align' => 'right', 'width' => '48'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        default => 'origin-top-right right-0',
    };

    $widthClass = match ($width) {
        '48' => 'w-48',
        default => 'w-48',
    };
@endphp

<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @click.away="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 rounded-md shadow-lg {{ $alignmentClasses }} {{ $widthClass }}"
        style="display: none;"
    >
        <div class="rounded-md bg-[#2d2d44] text-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-700">
            {{ $content }}
        </div>
    </div>
</div>
