@props(['label' => '', 'name' => '', 'type' => 'text'])

<div>
    <div class="relative">
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            placeholder=" "
            {{ $attributes->merge(['class' => 'peer block w-full min-h-touch rounded-lg border border-slate-300 bg-white px-3 pt-5 pb-2 text-sm text-slate-900 placeholder-transparent focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50']) }}
        />
        <label
            for="{{ $name }}"
            class="pointer-events-none absolute left-3 top-2 text-xs font-medium text-slate-500 transition-all peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-2 peer-focus:translate-y-0 peer-focus:text-xs peer-focus:text-indigo-600"
        >{{ $label }}</label>
    </div>
    @error($name)
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
