@php
    $tabs = [
        'pages' => ['label' => 'Pages', 'icon' => 'fa-file-lines', 'route' => 'school-admin.cms.pages'],
        'posts' => ['label' => 'Blog', 'icon' => 'fa-newspaper', 'route' => 'school-admin.cms.posts'],
        'gallery' => ['label' => 'Gallery', 'icon' => 'fa-images', 'route' => 'school-admin.cms.gallery'],
        'messages' => ['label' => 'Messages', 'icon' => 'fa-envelope', 'route' => 'school-admin.cms.messages'],
    ];
    $school = auth()->user()->school;
@endphp

<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Website (CMS)</h1>
            <p class="text-sm text-slate-500 mt-1">Manage your school's public website content.</p>
        </div>
        @if ($school)
            <a href="{{ route('public.site.home', $school) }}" target="_blank" class="btn-secondary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Public Site
            </a>
        @endif
    </div>

    <div class="flex flex-wrap gap-2 mt-4">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route($tab['route']) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium {{ $active === $key ? 'brand-gradient text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
            </a>
        @endforeach
        <a href="{{ route('school-admin.settings') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-slate-100 text-slate-600 hover:bg-slate-200">
            <i class="fa-solid fa-house"></i> Homepage Hero
        </a>
    </div>
</div>
