<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-comments text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Messages</h1>
                <p class="text-sm text-white/80 mt-0.5">
                    {{ $isTeacher ? 'Message parents of your students.' : "Message your children's teachers." }}
                </p>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden" style="height: min(70vh, 640px)">
        <div class="flex h-full">
            <!-- Conversation list -->
            <div class="{{ $showList ? 'flex' : 'hidden' }} lg:flex flex-col w-full lg:w-80 lg:shrink-0 border-r border-slate-100">
                <div class="p-3 border-b border-slate-100 space-y-2">
                    <select wire:model="newContact" class="w-full min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                        <option value="">Start a new conversation…</option>
                        @foreach ($contacts as $contact)
                            <option value="{{ $contact['value'] }}">{{ $contact['label'] }}</option>
                        @endforeach
                    </select>
                    <button type="button" wire:click="startConversation" class="btn-secondary w-full justify-center text-sm" @disabled(! $newContact)>
                        <i class="fa-solid fa-plus"></i> Start conversation
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    @forelse ($conversations as $conversation)
                        <button type="button" wire:click="selectConversation({{ $conversation->id }})"
                            class="w-full text-left p-3 hover:bg-slate-50 transition-colors {{ $activeConversation?->id === $conversation->id ? 'bg-slate-50' : '' }}">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-medium text-sm text-slate-800 truncate">
                                    {{ $isTeacher ? $conversation->guardian->user->name : $conversation->teacher->user->name }}
                                </p>
                                @if ($conversation->unread_count > 0)
                                    <span class="shrink-0 h-5 min-w-[20px] px-1 rounded-full bg-rose-500 text-white text-[11px] font-semibold flex items-center justify-center">{{ $conversation->unread_count }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-0.5">Re: {{ $conversation->student->user->name }}</p>
                        </button>
                    @empty
                        <p class="p-6 text-sm text-slate-500 text-center">No conversations yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Thread -->
            <div class="{{ $showList ? 'hidden' : 'flex' }} lg:flex flex-col flex-1 min-w-0">
                @if ($activeConversation)
                    <div class="p-3 border-b border-slate-100 flex items-center gap-3">
                        <button type="button" wire:click="backToList" class="lg:hidden min-h-touch min-w-touch text-slate-500">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-slate-800 truncate">
                                {{ $isTeacher ? $activeConversation->guardian->user->name : $activeConversation->teacher->user->name }}
                            </p>
                            <p class="text-xs text-slate-500">Re: {{ $activeConversation->student->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-3" wire:poll.5s>
                        @forelse ($messages as $msg)
                            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[75%] rounded-2xl px-4 py-2 text-sm {{ $msg->sender_id === auth()->id() ? 'brand-gradient text-white' : 'bg-slate-100 text-slate-700' }}">
                                    <p class="whitespace-pre-line">{{ $msg->body }}</p>
                                    <p class="text-[10px] mt-1 {{ $msg->sender_id === auth()->id() ? 'text-white/70' : 'text-slate-400' }}">{{ $msg->created_at->format('d M, h:i A') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 text-center py-10">No messages yet. Say hello!</p>
                        @endforelse
                    </div>

                    <form wire:submit="send" class="p-3 border-t border-slate-100 flex gap-2">
                        <input type="text" wire:model="body" placeholder="Type a message…"
                            class="flex-1 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                        <button type="submit" class="btn-primary shrink-0">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                    @error('body') <p class="px-3 pb-2 text-xs text-rose-600">{{ $message }}</p> @enderror
                @else
                    <div class="flex-1 flex items-center justify-center text-slate-400 text-sm p-6 text-center">
                        Select a conversation, or start a new one to get in touch.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
