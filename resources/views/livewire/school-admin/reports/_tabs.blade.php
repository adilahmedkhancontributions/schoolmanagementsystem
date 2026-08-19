@php
    $tabs = [
        'attendance' => ['label' => 'Attendance', 'icon' => 'fa-calendar-check', 'route' => 'school-admin.reports.attendance'],
        'exams' => ['label' => 'Exams', 'icon' => 'fa-graduation-cap', 'route' => 'school-admin.reports.exams'],
        'fees' => ['label' => 'Fees', 'icon' => 'fa-file-invoice-dollar', 'route' => 'school-admin.reports.fees'],
    ];
@endphp

<div class="mb-6">
    <h1 class="font-heading text-2xl font-bold text-slate-900">Reports</h1>
    <p class="text-sm text-slate-500 mt-1 mb-4">Attendance, academic and financial insights for your school.</p>

    <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium {{ $active === $key ? 'brand-gradient text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>
