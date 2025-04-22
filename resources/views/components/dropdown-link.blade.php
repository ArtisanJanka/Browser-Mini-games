@props(['href'])

<a {{ $attributes->merge([
    'href' => $href,
    'class' => 'block px-4 py-2 text-sm text-white hover:bg-[#3a3a55] hover:text-green-300 transition duration-150 ease-in-out'
]) }}>
    {{ $slot }}
</a>
