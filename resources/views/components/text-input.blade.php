@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-300 bg-white/90 shadow-sm transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-emerald-600 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500']) }}>
