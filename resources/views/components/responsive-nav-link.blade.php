@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg px-3 py-2 text-start text-base font-semibold text-blue-700 bg-blue-50 ring-1 ring-blue-100 transition'
            : 'block w-full rounded-lg px-3 py-2 text-start text-base font-medium text-slate-600 transition hover:bg-blue-50/80 hover:text-blue-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
