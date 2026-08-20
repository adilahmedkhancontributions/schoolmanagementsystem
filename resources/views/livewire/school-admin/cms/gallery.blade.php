<div>
    @include('livewire.school-admin.cms._tabs', ['active' => 'gallery'])

    <div class="card p-5 mb-6">
        <h2 class="font-heading font-bold text-slate-900 mb-3">Upload images</h2>
        <form wire:submit="upload" class="space-y-3">
            <input type="file" wire:model="newImages" multiple accept="image/*"
                class="block w-full text-sm text-slate-600 file:mr-3 file:min-h-touch file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
            @error('newImages.*') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
            @error('newImages') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror

            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" wire:model="caption" placeholder="Caption (optional, applies to all selected)"
                    class="w-full sm:flex-1 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="upload,newImages">
                    <i class="fa-solid fa-upload"></i> Upload
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($images as $index => $image)
            <div class="card overflow-hidden">
                <img src="{{ $image->imageUrl() }}" class="h-32 w-full object-cover" alt="{{ $image->caption }}">
                <div class="p-3">
                    <p class="text-xs text-slate-500 truncate">{{ $image->caption ?: 'Untitled' }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-1">
                            <button type="button" wire:click="moveUp({{ $image->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Move up">
                                <i class="fa-solid fa-arrow-up"></i>
                            </button>
                            <button type="button" wire:click="moveDown({{ $image->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Move down">
                                <i class="fa-solid fa-arrow-down"></i>
                            </button>
                        </div>
                        <button type="button" wire:click="delete({{ $image->id }})" wire:confirm="Delete this image?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-10 text-center text-slate-500 col-span-full">No gallery images yet.</div>
        @endforelse
    </div>
</div>
