<?php

namespace App\Livewire\Leave;

use App\Models\LeaveRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class MyLeave extends Component
{
    public ?int $studentId = null;

    public string $fromDate = '';

    public string $toDate = '';

    public string $reason = '';

    public bool $showForm = false;

    public function mount(): void
    {
        $user = auth()->user();

        if ($user->hasRole('parent')) {
            $this->studentId = $user->guardianProfile?->students()->first()?->id;
        }
    }

    public function render(): View
    {
        $user = auth()->user();

        $children = $user->hasRole('parent')
            ? $user->guardianProfile?->students()->with('user')->get() ?? collect()
            : collect();

        if ($user->hasRole('parent')) {
            $allowedIds = $children->pluck('id');
            if (! $allowedIds->contains($this->studentId)) {
                $this->studentId = $allowedIds->first();
            }
        }

        $query = LeaveRequest::where('user_id', $user->id)->latest();

        if ($user->hasRole('parent') && $this->studentId) {
            $query->where('student_id', $this->studentId);
        }

        return view('livewire.leave.my-leave', [
            'children' => $children,
            'requests' => $query->get(),
        ]);
    }

    public function openForm(): void
    {
        $this->fromDate = '';
        $this->toDate = '';
        $this->reason = '';
        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
    }

    public function submit(): void
    {
        $user = auth()->user();

        if ($user->hasRole('parent')) {
            $allowedIds = $user->guardianProfile?->students()->pluck('students.id') ?? collect();
            abort_unless($allowedIds->contains($this->studentId), 403);
        }

        $validated = $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
            'reason' => 'required|string|max:1000',
        ]);

        LeaveRequest::create([
            'school_id' => $user->school_id,
            'user_id' => $user->id,
            'student_id' => $user->hasRole('parent') ? $this->studentId : null,
            'from_date' => $validated['fromDate'],
            'to_date' => $validated['toDate'],
            'reason' => $validated['reason'],
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        $this->closeForm();
        session()->flash('message', 'Leave request submitted.');
    }
}
