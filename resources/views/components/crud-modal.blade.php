@props(['show' => false, 'wireClose' => null, 'title' => '', 'maxWidth' => 'lg'])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth] ?? 'max-w-lg';
@endphp

@if ($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60" @if($wireClose) wire:click="{{ $wireClose }}" @endif></div>

        <div class="relative w-full {{ $maxWidthClass }} card p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-bold text-lg text-slate-900">{{ $title }}</h3>
                @if ($wireClose)
                    <button type="button" wire:click="{{ $wireClose }}" class="min-h-touch min-w-touch text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                @endif
            </div>

            {{ $slot }}
        </div>
    </div>
@endif
