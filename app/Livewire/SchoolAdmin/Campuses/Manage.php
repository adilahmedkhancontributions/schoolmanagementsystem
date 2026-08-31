<?php

namespace App\Livewire\SchoolAdmin\Campuses;

use App\Models\Campus;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    public bool $showModal = false;

    public ?int $campusId = null;

    public string $name = '';

    public string $code = '';

    public string $address = '';

    public string $city = '';

    public string $phone = '';

    public bool $isDefault = false;

    public string $status = 'active';

    public function render(): View
    {
        $campuses = Campus::where('school_id', auth()->user()->school_id)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('livewire.school-admin.campuses.manage', ['campuses' => $campuses]);
    }

    public function openCreate(): void
    {
        $this->resetExcept(['campuses']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $campus = Campus::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->campusId = $campus->id;
        $this->name = $campus->name;
        $this->code = (string) $campus->code;
        $this->address = (string) $campus->address;
        $this->city = (string) $campus->city;
        $this->phone = (string) $campus->phone;
        $this->isDefault = (bool) $campus->is_default;
        $this->status = $campus->status;

        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:120',
            'code' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:40',
            'isDefault' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $schoolId = auth()->user()->school_id;

        $campus = Campus::updateOrCreate(
            ['id' => $this->campusId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'name' => $validated['name'],
                'code' => $validated['code'] ?: null,
                'address' => $validated['address'] ?: null,
                'city' => $validated['city'] ?: null,
                'phone' => $validated['phone'] ?: null,
                'status' => $validated['status'],
            ]
        );

        if ($validated['isDefault']) {
            Campus::where('school_id', $schoolId)->update(['is_default' => false]);
            $campus->update(['is_default' => true]);
        }

        $this->showModal = false;
        $this->resetExcept(['campuses']);
    }

    public function setDefault(int $id): void
    {
        $schoolId = auth()->user()->school_id;
        Campus::where('school_id', $schoolId)->findOrFail($id);
        Campus::where('school_id', $schoolId)->update(['is_default' => false]);
        Campus::where('school_id', $schoolId)->where('id', $id)->update(['is_default' => true]);
    }

    public function delete(int $id): void
    {
        $campus = Campus::where('school_id', auth()->user()->school_id)->findOrFail($id);

        if ($campus->is_default) {
            session()->flash('error', 'You cannot delete the default campus. Make another campus default first.');
            return;
        }

        $campus->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetExcept(['campuses']);
    }
}
