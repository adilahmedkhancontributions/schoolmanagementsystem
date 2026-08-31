@props(['items' => [], 'route' => null])

@php
    $role = auth()->user()->getRoleNames()->first();
    $groups = $items ?: \App\Support\Navigation::forRole($role);
    $current = $route ?: request()->route()?->getName();
@endphp

<nav x-data="{ open: @js(collect($groups)->mapWithKeys(fn ($g) => [$g['label'] => true])->all()) }" class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
    @foreach ($groups as $group)
        <div>
            <button type="button" @click="open['{{ $group['label'] }}'] = !open['{{ $group['label'] }}']"
                class="group-label w-full flex items-center justify-between gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 hover:text-slate-200 px-2 py-1.5 rounded-md">
                <span class="flex items-center gap-2">
                    <i class="fa-solid {{ $group['icon'] }} text-xs"></i>
                    <span>{{ $group['label'] }}</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] transition-transform" :class="open['{{ $group['label'] }}'] && 'rotate-90'"></i>
            </button>

            <div x-show="open['{{ $group['label'] }}']" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-1 space-y-1">
                @foreach ($group['items'] as $item)
                    @php($isActive = $item['route'] && $current && \Illuminate\Support\Str::startsWith($current, preg_replace('/\..*$/', '', $item['route']) . '.'))
                    @php($isExact = $item['route'] && $current === $item['route'])
                    @if ($item['route'] && \Illuminate\Support\Facades\Route::has($item['route']))
                        <a href="{{ route($item['route']) }}" class="sidebar-link {{ ($isActive || $isExact) ? 'active' : '' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span class="sidebar-link opacity-40 cursor-not-allowed" title="Coming soon">
                            <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                            <span>{{ $item['label'] }}</span>
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
