@props(['placeholder' => 'Cari...', 'modelValue' => null])

<div x-data="{ q: @js(request()->query('q', '')) }" class="w-full sm:w-72">
    <div class="relative">
        <div class="absolute inset-y-0 start-0 ps-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
        <input
            type="search"
            name="q"
            value="{{ request()->query('q') }}"
            x-model="q"
            @keydown.enter="$el.form && $el.form.submit()"
            placeholder="{{ $placeholder }}"
            class="form-input-custom block w-full ps-9 pe-8"
        />
        @if(request()->filled('q'))
            <a href="{{ url()->current() }}" title="Hapus pencarian"
               class="absolute inset-y-0 end-0 pe-2.5 flex items-center text-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        @endif
    </div>
</div>
