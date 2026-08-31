<?php

namespace App\Livewire\SchoolAdmin\Leave;

use App\Models\LeaveRequest;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    public string $statusFilter = 'pending';

    public string $typeFilter = 'all';

    public ?int $reviewingId = null;

    public string $adminNote = '';

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $query = LeaveRequest::where('school_id', $schoolId)
            ->with(['user', 'student.user'])
            ->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter === 'student') {
            $query->whereNotNull('student_id');
        } elseif ($this->typeFilter === 'staff') {
            $query->whereNull('student_id');
        }

        return view('livewire.school-admin.leave.manage', [
            'requests' => $query->get(),
        ]);
    }

    public function approve(int $requestId): void
    {
        $schoolId = auth()->user()->school_id;
        $request = LeaveRequest::where('school_id', $schoolId)->findOrFail($requestId);

        if ($request->status !== LeaveRequest::STATUS_PENDING) {
            return;
        }

        $request->update([
            'status' => LeaveRequest::STATUS_APPROVED,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        session()->flash('message', 'Leave request approved.');
    }

    public function startReject(int $requestId): void
    {
        $this->reviewingId = $requestId;
        $this->adminNote = '';
    }

    public function cancelReject(): void
    {
        $this->reviewingId = null;
        $this->adminNote = '';
    }

    public function reject(): void
    {
        $schoolId = auth()->user()->school_id;
        $request = LeaveRequest::where('school_id', $schoolId)->findOrFail($this->reviewingId);

        if ($request->status !== LeaveRequest::STATUS_PENDING) {
            $this->cancelReject();

            return;
        }

        $request->update([
            'status' => LeaveRequest::STATUS_REJECTED,
            'admin_note' => $this->adminNote ?: null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $this->cancelReject();
        session()->flash('message', 'Leave request rejected.');
    }
}
