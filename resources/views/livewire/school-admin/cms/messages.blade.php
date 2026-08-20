<div>
    @include('livewire.school-admin.cms._tabs', ['active' => 'messages'])

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($messages as $message)
            <div class="card p-4 {{ $message->is_read ? '' : 'border-l-4 border-l-indigo-500' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $message->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $message->email }}{{ $message->phone ? ' · '.$message->phone : '' }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @unless ($message->is_read)
                            <button type="button" wire:click="markRead({{ $message->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Mark read">
                                <i class="fa-solid fa-envelope-open"></i>
                            </button>
                        @endunless
                        <button type="button" wire:click="delete({{ $message->id }})" wire:confirm="Delete this message?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $message->message }}</p>
                <p class="text-xs text-slate-400 mt-2">{{ $message->created_at->format('d M Y, h:i A') }}</p>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No messages yet.</div>
        @endforelse
    </div>

    <!-- Desktop list -->
    <div class="hidden sm:block space-y-3">
        @forelse ($messages as $message)
            <div class="card p-5 {{ $message->is_read ? '' : 'border-l-4 border-l-indigo-500' }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-medium text-slate-800">{{ $message->name }}</p>
                        <p class="text-xs text-slate-500">{{ $message->email }}{{ $message->phone ? ' · '.$message->phone : '' }} &middot; {{ $message->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @unless ($message->is_read)
                            <button type="button" wire:click="markRead({{ $message->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600" title="Mark read">
                                <i class="fa-solid fa-envelope-open"></i>
                            </button>
                        @endunless
                        <button type="button" wire:click="delete({{ $message->id }})" wire:confirm="Delete this message?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mt-3 whitespace-pre-line">{{ $message->message }}</p>
            </div>
        @empty
            <div class="card p-10 text-center text-slate-500">No messages yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $messages->links() }}</div>
</div>
