<div>
    @include('livewire.school-admin.cms._tabs', ['active' => 'posts'])

    <div class="flex justify-end mb-4">
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Post
        </button>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($posts as $post)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $post->title }}</p>
                        <p class="text-xs text-slate-500">{{ $post->published_at?->format('d M Y') ?? 'Not scheduled' }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openEdit({{ $post->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Delete this post?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <span class="inline-block mt-2 text-xs font-semibold px-2 py-1 rounded-full {{ $post->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ ucfirst($post->status) }}
                </span>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No blog posts yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Published</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($posts as $post)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $post->title }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $post->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $post->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="Delete this post?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-500">No blog posts yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$postId ? 'Edit Post' : 'Add Post'" maxWidth="2xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Title" name="title" wire:model.live.debounce.400ms="title" />
                <x-floating-input label="Slug" name="slug" wire:model="slug" />
            </div>

            <x-floating-input label="Excerpt (optional, shown in blog list)" name="excerpt" wire:model="excerpt" />

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">Featured image</label>
                <div class="flex items-center gap-3">
                    @if ($featuredImage)
                        <img src="{{ $featuredImage->temporaryUrl() }}" class="h-14 w-14 rounded-lg object-cover">
                    @elseif ($existingFeaturedImageUrl)
                        <img src="{{ $existingFeaturedImageUrl }}" class="h-14 w-14 rounded-lg object-cover">
                    @else
                        <div class="h-14 w-14 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-image"></i>
                        </div>
                    @endif
                    <input type="file" wire:model="featuredImage" accept="image/*" class="text-sm text-slate-600 file:mr-3 file:min-h-touch file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                </div>
                @error('featuredImage') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <x-rich-text-editor wireModel="content" label="Post content" :value="$content" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <x-floating-select label="Status" name="status" wire:model.live="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </x-floating-select>

                @if ($status === 'published')
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="publishNow" wire:model.live="publishNow" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="publishNow" class="text-sm text-slate-700">Publish immediately</label>
                    </div>
                @endif
            </div>

            @if ($status === 'published' && ! $publishNow)
                <x-floating-input label="Publish on" name="publishDate" type="datetime-local" wire:model="publishDate" />
            @endif

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
