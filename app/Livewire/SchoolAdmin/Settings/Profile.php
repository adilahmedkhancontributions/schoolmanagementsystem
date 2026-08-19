<?php

namespace App\Livewire\SchoolAdmin\Settings;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $city = '';

    public string $country = '';

    public string $academicYear = '';

    public string $primaryColor = '#4f46e5';

    public string $secondaryColor = '#0ea5e9';

    public $logo = null;

    public bool $saved = false;

    public function mount(): void
    {
        $school = $this->school();

        $this->name = $school->name;
        $this->email = (string) $school->email;
        $this->phone = (string) $school->phone;
        $this->address = (string) $school->address;
        $this->city = (string) $school->city;
        $this->country = (string) $school->country;
        $this->academicYear = (string) $school->academic_year;
        $this->primaryColor = $school->primary_color;
        $this->secondaryColor = $school->secondary_color;
    }

    public function render(): View
    {
        return view('livewire.school-admin.settings.profile', [
            'school' => $this->school(),
        ]);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'academicYear' => 'nullable|string|max:20',
            'primaryColor' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'secondaryColor' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?: null,
            'phone' => $validated['phone'] ?: null,
            'address' => $validated['address'] ?: null,
            'city' => $validated['city'] ?: null,
            'country' => $validated['country'] ?: null,
            'academic_year' => $validated['academicYear'] ?: null,
            'primary_color' => $validated['primaryColor'],
            'secondary_color' => $validated['secondaryColor'],
        ];

        if ($this->logo) {
            $data['logo'] = $this->logo->store('school-logos', 'public');
        }

        $this->school()->update($data);

        $this->logo = null;
        $this->saved = true;
    }

    private function school(): School
    {
        return School::findOrFail(auth()->user()->school_id);
    }
}
