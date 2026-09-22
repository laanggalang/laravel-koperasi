@props(['active', 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold bg-indigo-50 text-indigo-700 focus:outline-none transition duration-150 ease-in-out'
            : 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:text-gray-900 focus:bg-gray-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="w-5 h-5 shrink-0 [&>svg]:w-5 [&>svg]:h-5">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
