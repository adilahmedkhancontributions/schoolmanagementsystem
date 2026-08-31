@php
    $tabs = [
        'attendance' => ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'school-admin.reports.attendance'],
        'exams' => ['label' => 'Exams', 'icon' => 'fa-graduation-cap', 'route' => 'school-admin.reports.exams'],
        'fees' => ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'school-admin.reports.fees'],
    ];
@endphp

<div class="mb-6">
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-4">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-chart-column text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Reports</h1>
                <p class="text-sm text-white/80 mt-0.5">Attendance, academic and financial insights for your school.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium {{ $active === $key ? 'brand-gradient text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>
