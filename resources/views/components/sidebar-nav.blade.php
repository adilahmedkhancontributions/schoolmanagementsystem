@props(['items' => [], 'route' => null])

@php
    $role = auth()->user()->getRoleNames()->first();
    $groups = $items ?: \App\Support\Navigation::forRole($role);
    $current = $route ?: request()->route()?->getName();

    // First pass: find the exact-match item (preferred over prefix match)
    $activeGroup = null;
    $activeItem = null;
    foreach ($groups as $group) {
        foreach ($group['items'] as $item) {
            if (! $item['route']) {
                continue;
            }
            if ($current && $current === $item['route']) {
                $activeGroup = $group['label'];
                $activeItem = $item['route'];
                break 2;
            }
        }
    }
    // Second pass: if no exact match, fall back to prefix match
    // (for child routes like reports.attendance under the "Reports" item)
    if (! $activeGroup) {
        foreach ($groups as $group) {
            foreach ($group['items'] as $item) {
                if (! $item['route']) {
                    continue;
                }
                $prefix = rtrim($item['route'], '.') . '.';
                if ($current && \Illuminate\Support\Str::startsWith($current, $prefix)) {
                    $activeGroup = $group['label'];
                    $activeItem = $item['route'];
                    break 2;
                }
            }
        }
    }
@endphp

<nav x-data="{ open: @js(collect($groups)->mapWithKeys(fn ($g) => [$g['label'] => $g['label'] === $activeGroup])->all()) }" class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
    @foreach ($groups as $group)
        @php
            $isGroupActive = $group['label'] === $activeGroup;
        @endphp
        <div>
            <button type="button" @click="open['{{ $group['label'] }}'] = !open['{{ $group['label'] }}']"
                class="group-label w-full flex items-center justify-between gap-2 text-[11px] font-semibold uppercase tracking-wider px-2 py-1.5 rounded-md {{ $isGroupActive ? 'text-white' : 'text-slate-400 hover:text-slate-200' }}">
                <span class="flex items-center gap-2">
                    <i class="fa-solid {{ $group['icon'] }} text-xs"></i>
                    <span>{{ $group['label'] }}</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-150" :class="open['{{ $group['label'] }}'] ? 'rotate-90' : ''"></i>
            </button>

            <div x-show="open['{{ $group['label'] }}']"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mt-1 space-y-1">
                @foreach ($group['items'] as $item)
                    @php
                        $prefix = rtrim($item['route'] ?? '', '.') . '.';
                        $isActive = $item['route'] && $current && ($current === $item['route'] || \Illuminate\Support\Str::startsWith($current, $prefix));
                    @endphp
                    @if ($item['route'] && \Illuminate\Support\Facades\Route::has($item['route']))
                        <a href="{{ route($item['route']) }}" class="sidebar-link {{ $isActive ? 'active' : '' }}" aria-current="{{ $isActive ? 'page' : 'false' }}">
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
