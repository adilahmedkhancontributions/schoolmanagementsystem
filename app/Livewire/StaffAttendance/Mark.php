<?php

namespace App\Livewire\StaffAttendance;

use App\Models\Staff;
use App\Models\StaffAttendance as StaffAttendanceModel;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Mark extends Component
{
    public string $date;

    public array $status = [];

    public array $remarks = [];

    public bool $saved = false;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
        $this->loadAttendance();
    }

    public function updatedDate(): void
    {
        $this->loadAttendance();
    }

    public function render(): View
    {
        return view('livewire.staff-attendance.mark', [
            'members' => $this->staffMembers(),
        ]);
    }

    private function staffMembers(): Collection
    {
        $schoolId = auth()->user()->school_id;

        $teachers = Teacher::with('user')->where('school_id', $schoolId)->get()
            ->map(fn ($teacher) => [
                'user_id' => $teacher->user_id,
                'name' => $teacher->user->name,
                'role' => 'Teacher',
                'designation' => $teacher->specialization ?: 'Teacher',
            ]);

        $staff = Staff::with('user')->where('school_id', $schoolId)->get()
            ->map(fn ($member) => [
                'user_id' => $member->user_id,
                'name' => $member->user->name,
                'role' => 'Staff',
                'designation' => $member->designation,
            ]);

        return $teachers->concat($staff)->sortBy('name')->values();
    }

    public function loadAttendance(): void
    {
        $this->saved = false;
        $this->status = [];
        $this->remarks = [];

        $schoolId = auth()->user()->school_id;

        $existing = StaffAttendanceModel::where('school_id', $schoolId)
            ->whereDate('date', $this->date)
            ->get()
            ->keyBy('user_id');

        foreach ($this->staffMembers() as $member) {
            $record = $existing->get($member['user_id']);
            $this->status[$member['user_id']] = $record->status ?? 'present';
            $this->remarks[$member['user_id']] = $record->remarks ?? '';
        }
    }

    public function markAll(string $status): void
    {
        foreach (array_keys($this->status) as $userId) {
            $this->status[$userId] = $status;
        }
    }

    public function setStatus(int $userId, string $status): void
    {
        $this->status[$userId] = $status;
    }

    public function save(): void
    {
        $this->validate(['date' => 'required|date']);

        $schoolId = auth()->user()->school_id;
        $memberIds = $this->staffMembers()->pluck('user_id');

        foreach ($this->status as $userId => $status) {
            if (! $memberIds->contains($userId)) {
                continue;
            }

            $attendance = StaffAttendanceModel::where('user_id', $userId)
                ->whereDate('date', $this->date)
                ->first() ?? new StaffAttendanceModel(['user_id' => $userId, 'date' => $this->date]);

            $attendance->fill([
                'school_id' => $schoolId,
                'status' => $status,
                'remarks' => $this->remarks[$userId] ?: null,
                'marked_by' => auth()->id(),
            ])->save();
        }

        $this->saved = true;
    }
}
