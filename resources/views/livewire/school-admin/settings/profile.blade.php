<div>
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-slate-900">School Profile</h1>
        <p class="text-sm text-slate-500 mt-1">Update your school's contact info, logo and theme colors.</p>
    </div>

    @if ($saved)
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            School profile updated. Reload the page to see the new theme everywhere.
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="card p-6">
            <h2 class="font-heading font-bold text-slate-900 mb-4">Contact information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="School name" name="name" wire:model="name" />
                <x-floating-input label="Email" name="email" type="email" wire:model="email" />
                <x-floating-input label="Phone" name="phone" wire:model="phone" />
                <x-floating-input label="City" name="city" wire:model="city" />
                <x-floating-input label="Country" name="country" wire:model="country" />
                <x-floating-input label="Address" name="address" wire:model="address" />
                <x-floating-input label="Academic year (e.g. 2024-2025)" name="academicYear" wire:model="academicYear" />
            </div>
        </div>

        <div class="card p-6">
            <h2 class="font-heading font-bold text-slate-900 mb-4">Branding &amp; theme</h2>

            <div class="flex items-center gap-4 mb-6">
                @if ($logo)
                    <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-16 rounded-xl object-cover">
                @elseif ($school->logoUrl())
                    <img src="{{ $school->logoUrl() }}" class="h-16 w-16 rounded-xl object-cover">
                @else
                    <div class="h-16 w-16 rounded-xl flex items-center justify-center font-heading font-bold text-white text-xl"
                         style="background-image: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }})">
                        {{ strtoupper(substr($school->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">Logo</label>
                    <input type="file" wire:model="logo" accept="image/*" class="text-sm text-slate-600 file:mr-3 file:min-h-touch file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                    @error('logo') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">Primary color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model="primaryColor" class="h-11 w-11 rounded-lg border border-slate-300 p-1">
                        <input type="text" wire:model="primaryColor" class="min-h-touch flex-1 rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    </div>
                    @error('primaryColor') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">Secondary color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model="secondaryColor" class="h-11 w-11 rounded-lg border border-slate-300 p-1">
                        <input type="text" wire:model="secondaryColor" class="min-h-touch flex-1 rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                    </div>
                    @error('secondaryColor') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="font-heading font-bold text-slate-900 mb-1">Public website homepage</h2>
            <p class="text-sm text-slate-500 mb-4">Shown on your public site's hero banner at <a href="{{ route('public.site.home', $school) }}" target="_blank" class="brand-text hover:underline">{{ route('public.site.home', $school) }}</a>.</p>

            <div class="space-y-4">
                <x-floating-input label="Hero headline (optional)" name="heroHeadline" wire:model="heroHeadline" />
                <x-floating-input label="Hero subheadline (optional)" name="heroSubheadline" wire:model="heroSubheadline" />

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">Hero image</label>
                    <div class="flex items-center gap-3">
                        @if ($heroImage)
                            <img src="{{ $heroImage->temporaryUrl() }}" class="h-16 w-24 rounded-lg object-cover">
                        @elseif ($school->heroImageUrl())
                            <img src="{{ $school->heroImageUrl() }}" class="h-16 w-24 rounded-lg object-cover">
                        @else
                            <div class="h-16 w-24 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                        <input type="file" wire:model="heroImage" accept="image/*" class="text-sm text-slate-600 file:mr-3 file:min-h-touch file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                    @error('heroImage') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Save changes</button>
        </div>
    </form>
</div>
