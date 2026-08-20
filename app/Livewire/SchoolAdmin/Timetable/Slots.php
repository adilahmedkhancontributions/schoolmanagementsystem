<?php

namespace App\Livewire\SchoolAdmin\Timetable;

use App\Models\TimetableSlot;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Slots extends Component
{
    public bool $showModal = false;

    public ?int $slotId = null;

    public string $name = '';

    public string $startTime = '';

    public string $endTime = '';

    public function render(): View
    {
        return view('livewire.school-admin.timetable.slots', [
            'periods' => TimetableSlot::where('school_id', auth()->user()->school_id)
                ->orderBy('sort_order')
                ->orderBy('start_time')
                ->get(),
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $slot = TimetableSlot::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->slotId = $slot->id;
        $this->name = $slot->name;
        $this->startTime = $slot->start_time->format('H:i');
        $this->endTime = $slot->end_time->format('H:i');
        $this->showModal = true;
    }

    public function save(): void
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
        ]);

        $nextOrder = $this->slotId
            ? TimetableSlot::findOrFail($this->slotId)->sort_order
            : ((int) TimetableSlot::where('school_id', $schoolId)->max('sort_order') + 1);

        TimetableSlot::updateOrCreate(
            ['id' => $this->slotId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'name' => $validated['name'],
                'start_time' => $validated['startTime'],
                'end_time' => $validated['endTime'],
                'sort_order' => $nextOrder,
            ]
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        TimetableSlot::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['slotId', 'name', 'startTime', 'endTime']);
        $this->resetErrorBag();
    }
}
