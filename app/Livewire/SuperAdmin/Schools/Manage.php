<?php

namespace App\Livewire\SuperAdmin\Schools;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $schoolId = null;

    public string $name = '';

    public string $slug = '';

    public string $code = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $city = '';

    public string $country = '';

    public string $academicYear = '';

    public string $status = 'active';

    public string $primaryColor = '#4f46e5';

    public string $secondaryColor = '#0ea5e9';

    public $logo = null;

    public ?string $existingLogoUrl = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schools = School::withCount('students', 'teachers')
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(fn ($q2) => $q2->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%"));
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.super-admin.schools.manage', [
            'schools' => $schools,
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $school = School::findOrFail($id);

        $this->schoolId = $school->id;
        $this->name = $school->name;
        $this->slug = $school->slug;
        $this->code = $school->code;
        $this->email = (string) $school->email;
        $this->phone = (string) $school->phone;
        $this->address = (string) $school->address;
        $this->city = (string) $school->city;
        $this->country = (string) $school->country;
        $this->academicYear = (string) $school->academic_year;
        $this->status = $school->status;
        $this->primaryColor = $school->primary_color;
        $this->secondaryColor = $school->secondary_color;
        $this->existingLogoUrl = $school->logoUrl();
        $this->logo = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('schools', 'slug')->ignore($this->schoolId)],
            'code' => ['required', 'string', 'max:50', Rule::unique('schools', 'code')->ignore($this->schoolId)],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'academicYear' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'primaryColor' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'secondaryColor' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'code' => $validated['code'],
            'email' => $validated['email'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'address' => $validated['address'] ?: null,
            'city' => $validated['city'] ?: null,
            'country' => $validated['country'] ?: null,
            'academic_year' => $validated['academicYear'] ?: null,
            'status' => $validated['status'],
            'primary_color' => $validated['primaryColor'],
            'secondary_color' => $validated['secondaryColor'],
        ];

        if ($this->logo) {
            $data['logo'] = $this->logo->store('school-logos', 'public');
        }

        if ($this->schoolId) {
            School::findOrFail($this->schoolId)->update($data);
        } else {
            School::create($data);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        School::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'schoolId', 'name', 'slug', 'code', 'email', 'phone', 'address',
            'city', 'country', 'academicYear', 'logo', 'existingLogoUrl',
        ]);
        $this->status = 'active';
        $this->primaryColor = '#4f46e5';
        $this->secondaryColor = '#0ea5e9';
        $this->resetErrorBag();
    }
}
