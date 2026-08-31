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
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fa-solid fa-globe text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading text-xl sm:text-2xl font-bold">Website (CMS)</h1>
                    <p class="text-sm text-white/80 mt-0.5">Manage your school's public website content.</p>
                </div>
            </div>
            @if ($school)
                <a href="{{ route('public.site.home', $school) }}" target="_blank" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View Public Site
                </a>
            @endif
        </div>
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
