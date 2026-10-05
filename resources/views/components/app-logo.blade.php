@props(['class' => 'w-9 h-9'])

<img src="{{ asset('images/logo.png') }}" alt="Logo KABAPIN" {{ $attributes->merge(['class' => $class]) }}>
