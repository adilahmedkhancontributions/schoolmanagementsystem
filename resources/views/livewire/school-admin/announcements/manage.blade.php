<div>
    @php
        $audienceLabels = [
            'everyone' => 'Everyone',
            'teachers' => 'Teachers',
            'students' => 'Students',
            'parents' => 'Parents',
        ];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Announcements</h1>
            <p class="text-sm text-slate-500 mt-1">Publish notices to teachers, students and parents.</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> New Announcement
        </button>
    </div>

    @php
        $statusBadge = function ($announcement) {
            if ($announcement->isPublished()) {
                return ['label' => 'Published', 'class' => 'bg-emerald-50 text-emerald-700'];
            }
            if ($announcement->published_at) {
                return ['label' => 'Scheduled: '.$announcement->published_at->format('d M Y, h:i A'), 'class' => 'bg-amber-50 text-amber-700'];
            }
            return ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-500'];
        };
    @endphp

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($announcements as $announcement)
            @php($badge = $statusBadge($announcement))
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-medium text-slate-800 min-w-0 truncate">{{ $announcement->title }}</p>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="openEdit({{ $announcement->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" wire:click="delete({{ $announcement->id }})" wire:confirm="Delete this announcement?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-1">{{ $audienceLabels[$announcement->audience] }} &middot; {{ $announcement->schoolClass?->name ?? 'All classes' }}</p>
                <span class="inline-block mt-2 text-xs font-semibold px-2 py-1 rounded-full {{ $badge['class'] }}">{{ $badge['label'] }}</span>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No announcements yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Title</th>
                    <th class="py-3 px-4">Audience</th>
                    <th class="py-3 px-4">Class</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($announcements as $announcement)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">{{ $announcement->title }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $audienceLabels[$announcement->audience] }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $announcement->schoolClass?->name ?? 'All classes' }}</td>
                        <td class="py-3 px-4">
                            @if ($announcement->isPublished())
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">Published</span>
                            @elseif ($announcement->published_at)
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-50 text-amber-700">Scheduled: {{ $announcement->published_at->format('d M Y, h:i A') }}</span>
                            @else
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-500">Draft</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openEdit({{ $announcement->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" wire:click="delete({{ $announcement->id }})" wire:confirm="Delete this announcement?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">No announcements yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $announcements->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$announcementId ? 'Edit Announcement' : 'New Announcement'" maxWidth="xl">
        <form wire:submit="save" class="space-y-4">
            <x-floating-input label="Title" name="title" wire:model="title" />

            <div>
                <textarea wire:model="body" rows="5" placeholder="Write the announcement..."
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"></textarea>
                @error('body')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-floating-select label="Audience" name="audience" wire:model="audience">
                    @foreach ($audienceLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-floating-select>
                <x-floating-select label="Class (optional)" name="schoolClassId" wire:model="schoolClassId">
                    <option value="">All classes</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </x-floating-select>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="publishNow" wire:model.live="publishNow" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="publishNow" class="text-sm text-slate-700">Publish immediately</label>
            </div>

            @unless ($publishNow)
                <x-floating-input label="Publish on" name="publishDate" type="datetime-local" wire:model="publishDate" />
            @endunless

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>
</div>
