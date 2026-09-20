@props(['column', 'label', 'sortBy', 'sortDir'])

@php
    $isActive = $sortBy === $column;
    $nextDir = ($isActive && $sortDir === 'asc') ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort_by' => $column, 'sort_dir' => $nextDir]);
@endphp

<th scope="col">
    <a href="{{ $url }}"
       class="inline-flex items-center gap-1 hover:text-gray-800 transition-colors {{ $isActive ? 'text-indigo-600' : '' }}"
       @if($isActive) aria-label="Urutkan {{ $sortDir === 'asc' ? 'menurun' : 'menaik' }}" @endif>
        {{ $label }}

        @if($isActive)
            @if($sortDir === 'asc')
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            @endif
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm0 14a1 1 0 01-.707-.293l-3-3a1 1 0 111.414-1.414L10 14.586l2.293-2.293a1 1 0 111.414 1.414l-3 3A1 1 0 0110 17z" clip-rule="evenodd" />
            </svg>
        @endif
    </a>
</th>
