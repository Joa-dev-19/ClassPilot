@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-emerald-50 px-4 py-3 text-start text-sm font-semibold text-emerald-900 transition focus-visible:ring-2 focus-visible:ring-emerald-600'
            : 'block w-full rounded-xl px-4 py-3 text-start text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-950 focus-visible:ring-2 focus-visible:ring-emerald-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
