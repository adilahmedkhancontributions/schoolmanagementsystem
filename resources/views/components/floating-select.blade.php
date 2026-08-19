@props(['label' => '', 'name' => ''])

<div>
    <div class="relative">
        <select
            id="{{ $name }}"
            {{ $attributes->merge(['class' => 'peer block w-full min-h-touch rounded-lg border border-slate-300 bg-white px-3 pt-5 pb-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50']) }}
        >
            {{ $slot }}
        </select>
        <label
            for="{{ $name }}"
            class="pointer-events-none absolute left-3 top-1.5 text-xs font-medium text-slate-500"
        >{{ $label }}</label>
    </div>
    @error($name)
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
