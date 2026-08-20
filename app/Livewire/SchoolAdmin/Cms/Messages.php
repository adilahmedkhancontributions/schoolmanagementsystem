<?php

namespace App\Livewire\SchoolAdmin\Cms;

use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Messages extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.school-admin.cms.messages', [
            'messages' => ContactMessage::where('school_id', auth()->user()->school_id)
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    public function markRead(int $id): void
    {
        ContactMessage::where('school_id', auth()->user()->school_id)->findOrFail($id)->update(['is_read' => true]);
    }

    public function delete(int $id): void
    {
        ContactMessage::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }
}
