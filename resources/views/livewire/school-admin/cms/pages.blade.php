<div>
    @include('livewire.school-admin.cms._tabs', ['active' => 'pages'])

    <div class="flex justify-end mb-4">
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Add Page
        </button>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($pages as $page)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $page->title }}</p>
                        <p class="text-xs text-slate-500">/{{ $page->slug }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openEdit({{ $page->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $page->id }})" wire:confirm="Delete this page?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <span class="inline-block mt-2 text-xs font-semibold px-2 py-1 rounded-full {{ $page->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ ucfirst($page->status) }}
                </span>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No pages yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Slug</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pages as $page)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $page->title }}</td>
                        <td class="py-3 px-4 text-slate-500">/{{ $page->slug }}</td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $page->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $page->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $page->id }})" wire:confirm="Delete this page?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-500">No pages yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pages->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$pageId ? 'Edit Page' : 'Add Page'" maxWidth="2xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Title" name="title" wire:model.live.debounce.400ms="title" />
                <x-floating-input label="Slug" name="slug" wire:model="slug" />
            </div>

            <x-rich-text-editor wireModel="content" label="Page content" :value="$content" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <x-floating-input label="SEO meta title (optional)" name="metaTitle" wire:model="metaTitle" />
                <x-floating-input label="SEO meta description (optional)" name="metaDescription" wire:model="metaDescription" />
            </div>

            <x-floating-select label="Status" name="status" wire:model="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </x-floating-select>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
