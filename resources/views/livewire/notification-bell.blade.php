<div class="relative" wire:poll.30s x-data>
    <button wire:click="toggle" @click.outside="$wire.open = false" class="relative min-h-touch min-w-touch flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-600">
        <i class="fa-solid fa-bell text-lg"></i>
        @if ($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 h-4 min-w-[16px] px-1 rounded-full bg-rose-500 text-white text-[10px] leading-4 text-center font-semibold">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if ($open)
        <div class="absolute right-0 mt-2 w-80 max-w-[90vw] rounded-lg bg-white shadow-card-hover border border-slate-100 py-1 z-40">
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-slate-100">
                <span class="text-sm font-semibold text-slate-800">Notifications</span>
                @if ($unreadCount > 0)
                    <button wire:click="markAllRead" class="text-xs brand-text font-medium hover:underline">Mark all read</button>
                @endif
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <button
                        wire:click="markRead('{{ $notification->id }}')"
                        class="w-full text-left px-4 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-indigo-50/50' }}">
                        <p class="text-sm font-medium text-slate-800">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $notification->data['body'] ?? '' }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </button>
                @empty
                    <p class="px-4 py-6 text-center text-sm text-slate-400">No notifications yet.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
