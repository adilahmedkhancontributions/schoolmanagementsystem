@php
    $tabs = [
        'manage' => ['label' => 'Timetable', 'icon' => 'fa-calendar-days', 'route' => 'school-admin.timetable.manage'],
        'slots' => ['label' => 'Time Slots', 'icon' => 'fa-clock', 'route' => 'school-admin.timetable.slots'],
    ];
@endphp

<div class="mb-6">
    <h1 class="font-heading text-2xl font-bold text-slate-900">Timetable</h1>
    <p class="text-sm text-slate-500 mt-1 mb-4">Build your weekly class schedule.</p>

    <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium {{ $active === $key ? 'brand-gradient text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>
