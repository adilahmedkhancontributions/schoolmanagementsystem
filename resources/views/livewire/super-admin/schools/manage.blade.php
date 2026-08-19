<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Schools</h1>
            <p class="text-sm text-slate-500 mt-1">Manage schools, their branding and theme colors.</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add School
        </button>
    </div>

    <div class="mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by name or code..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        @forelse ($schools as $school)
            <div class="card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        @if ($school->logoUrl())
                            <img src="{{ $school->logoUrl() }}" class="h-12 w-12 rounded-xl object-cover" alt="{{ $school->name }}">
                        @else
                            <div class="h-12 w-12 rounded-xl flex items-center justify-center font-heading font-bold text-white"
                                 style="background-image: linear-gradient(135deg, {{ $school->primary_color }}, {{ $school->secondary_color }})">
                                {{ strtoupper(substr($school->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 truncate">{{ $school->name }}</p>
                            <p class="text-xs text-slate-500">{{ $school->code }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $school->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ ucfirst($school->status) }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-slate-500">
                    <div><i class="fa-solid fa-user-graduate mr-1"></i> {{ $school->students_count }} students</div>
                    <div><i class="fa-solid fa-chalkboard-user mr-1"></i> {{ $school->teachers_count }} teachers</div>
                </div>

                <div class="mt-4 flex items-center gap-2">
                    <span class="h-5 w-5 rounded-full border border-slate-200" style="background-color: {{ $school->primary_color }}"></span>
                    <span class="h-5 w-5 rounded-full border border-slate-200" style="background-color: {{ $school->secondary_color }}"></span>
                    <div class="ml-auto flex gap-1">
                        <button type="button" wire:click="openEdit({{ $school->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $school->id }})" wire:confirm="Delete this school and all of its data?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 col-span-full text-center py-10">No schools yet.</p>
        @endforelse
    </div>

    <div>{{ $schools->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$schoolId ? 'Edit School' : 'Add School'" maxWidth="xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="School name" name="name" wire:model="name" />
                <x-floating-input label="Slug" name="slug" wire:model="slug" />
                <x-floating-input label="Code" name="code" wire:model="code" />
                <x-floating-input label="Email" name="email" type="email" wire:model="email" />
                <x-floating-input label="Phone" name="phone" wire:model="phone" />
                <x-floating-input label="City" name="city" wire:model="city" />
                <x-floating-input label="Country" name="country" wire:model="country" />
                <x-floating-input label="Academic year (e.g. 2024-2025)" name="academicYear" wire:model="academicYear" />
                <x-floating-input label="Address" name="address" wire:model="address" />
                <x-floating-select label="Status" name="status" wire:model="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </x-floating-select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
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

            <div class="pt-2 border-t border-slate-100">
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">Logo</label>
                <div class="flex items-center gap-3">
                    @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" class="h-14 w-14 rounded-xl object-cover">
                    @elseif ($existingLogoUrl)
                        <img src="{{ $existingLogoUrl }}" class="h-14 w-14 rounded-xl object-cover">
                    @else
                        <div class="h-14 w-14 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-image"></i>
                        </div>
                    @endif
                    <input type="file" wire:model="logo" accept="image/*" class="text-sm text-slate-600 file:mr-3 file:min-h-touch file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                </div>
                @error('logo') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
