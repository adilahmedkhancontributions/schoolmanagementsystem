<?php

namespace App\Livewire\Announcements;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Feed extends Component
{
    use WithPagination;

    public function render(): View
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $classIds = match ($role) {
            'teacher' => $user->teacher?->sections()->pluck('school_class_id')->toArray() ?? [],
            'student' => $user->student ? [$user->student->school_class_id] : [],
            'parent' => $user->guardianProfile?->students()->pluck('school_class_id')->toArray() ?? [],
            default => [],
        };

        $announcements = Announcement::with(['schoolClass', 'author'])
            ->where('school_id', $user->school_id)
            ->visibleTo($role, $classIds)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('livewire.announcements.feed', [
            'announcements' => $announcements,
        ]);
    }
}
