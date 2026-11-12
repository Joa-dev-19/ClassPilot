@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-xl bg-emerald-50 px-3 py-2 text-sm font-semibold leading-5 text-emerald-900 transition focus-visible:ring-2 focus-visible:ring-emerald-600'
            : 'inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium leading-5 text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-emerald-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
