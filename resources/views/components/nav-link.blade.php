@props(['active', 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold bg-indigo-600 text-white shadow-sm shadow-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1 transition duration-150 ease-in-out'
            : 'inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-indigo-700 hover:bg-indigo-50 focus:outline-none focus:text-indigo-700 focus:bg-indigo-50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="w-4 h-4 [&>svg]:w-4 [&>svg]:h-4">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
