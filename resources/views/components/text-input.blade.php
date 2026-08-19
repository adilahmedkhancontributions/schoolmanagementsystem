@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full min-h-touch rounded-lg border-slate-300 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50']) }}>
