<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                    <i class="fa-solid fa-building text-lg"></i>
                </div>
                <div>
                    <h1 class="font-heading text-xl sm:text-2xl font-bold">Campuses</h1>
                    <p class="text-sm text-white/80 mt-0.5">Operate one school across multiple campuses. Assign classes, students and staff to a campus.</p>
                </div>
            </div>
            <button type="button" wire:click="openCreate" class="btn-secondary bg-white/15 text-white border-white/30 hover:bg-white/25">
                <i class="fa-solid fa-plus"></i> Add Campus
            </button>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="space-y-4">
        @forelse ($campuses as $campus)
            <div class="card p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-11 w-11 shrink-0 rounded-xl brand-gradient text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($campus->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-heading font-bold text-slate-900">{{ $campus->name }}</p>
                                @if ($campus->code)
                                    <span class="text-xs font-mono text-slate-400">{{ $campus->code }}</span>
                                @endif
                                @if ($campus->is_default)
                                    <span class="rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-0.5 text-xs font-medium">Default</span>
                                @endif
                                @if ($campus->status === 'inactive')
                                    <span class="rounded-full bg-slate-100 text-slate-500 px-2.5 py-0.5 text-xs font-medium">Inactive</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $campus->classes()->count() }} classes &middot; {{ $campus->students()->count() }} students &middot;
                                {{ $campus->teachers()->count() }} teachers &middot; {{ $campus->staff()->count() }} staff
                            </p>
                            @if ($campus->address || $campus->city)
                                <p class="text-xs text-slate-400 mt-1"><i class="fa-solid fa-location-dot mr-1"></i>{{ collect([$campus->address, $campus->city])->filter()->implode(', ') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @unless ($campus->is_default)
                            <button type="button" wire:click="setDefault({{ $campus->id }})" class="btn-secondary text-xs !py-1.5 !px-3" title="Make default">
                                <i class="fa-solid fa-star"></i> Make default
                            </button>
                        @endunless
                        <button type="button" wire:click="openEdit({{ $campus->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $campus->id }})" wire:confirm="Delete this campus (its classes, students, teachers and staff become campus-less, not deleted)?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-10 text-center">
                <div class="mx-auto mb-3 h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="fa-solid fa-building text-xl"></i>
                </div>
                <p class="text-slate-700 font-medium">No campuses yet</p>
                <p class="text-sm text-slate-500 mt-1 mb-4">Add your first campus — a default campus is created automatically when a school is set up.</p>
                <button type="button" wire:click="openCreate" class="btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Campus
                </button>
            </div>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog">
            <div class="fixed inset-0 bg-slate-900/60" wire:click="closeModal"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-heading text-lg font-bold text-slate-900">{{ $campusId ? 'Edit Campus' : 'Add Campus' }}</h2>
                    <button type="button" wire:click="closeModal" class="min-h-touch min-w-touch text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <x-floating-input label="Campus name" name="name" wire:model="name" />
                        @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-floating-input label="Campus code (optional)" name="code" wire:model="code" />
                        @error('code') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-floating-input label="Phone" name="phone" wire:model="phone" />
                        @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-floating-input label="Address" name="address" wire:model="address" />
                        @error('address') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-floating-input label="City" name="city" wire:model="city" />
                        @error('city') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" wire:model="isDefault" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                            Set as default campus
                        </label>
                        <select wire:model="status" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-check"></i> Save Campus</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
