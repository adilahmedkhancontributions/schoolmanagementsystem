<?php

namespace App\Livewire\SchoolAdmin\Students;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $filterClassId = null;

    public ?int $filterSectionId = null;

    public bool $showModal = false;

    public ?int $studentId = null;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $admissionNumber = '';

    public ?int $schoolClassId = null;

    public ?int $sectionId = null;

    public string $gender = 'male';

    public string $dateOfBirth = '';

    public ?string $generatedPassword = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClassId(): void
    {
        $this->filterSectionId = null;
        $this->resetPage();
    }

    public function updatingFilterSectionId(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $students = Student::with(['user', 'schoolClass', 'section'])
            ->where('school_id', $schoolId)
            ->when($this->filterClassId, fn ($q) => $q->where('school_class_id', $this->filterClassId))
            ->when($this->filterSectionId, fn ($q) => $q->where('section_id', $this->filterSectionId))
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('admission_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($q3) => $q3->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.school-admin.students.manage', [
            'students' => $students,
            'classes' => SchoolClass::where('school_id', $schoolId)->orderBy('sort_order')->get(),
            'sections' => $this->schoolClassId
                ? Section::where('school_class_id', $this->schoolClassId)->get()
                : collect(),
            'filterSections' => $this->filterClassId
                ? Section::where('school_class_id', $this->filterClassId)->get()
                : collect(),
        ]);
    }

    public function updatedSchoolClassId(): void
    {
        $this->sectionId = null;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $student = Student::with('user')->where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->studentId = $student->id;
        $this->userId = $student->user_id;
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->phone = (string) $student->user->phone;
        $this->admissionNumber = $student->admission_number;
        $this->schoolClassId = $student->school_class_id;
        $this->sectionId = $student->section_id;
        $this->gender = (string) $student->gender;
        $this->dateOfBirth = optional($student->date_of_birth)->format('Y-m-d') ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'phone' => 'nullable|string|max:30',
            'admissionNumber' => ['required', 'string', 'max:50', Rule::unique('students', 'admission_number')->ignore($this->studentId)],
            'schoolClassId' => 'nullable|exists:school_classes,id',
            'sectionId' => 'nullable|exists:sections,id',
            'gender' => 'nullable|in:male,female,other',
            'dateOfBirth' => 'nullable|date',
        ]);

        $schoolId = auth()->user()->school_id;

        if ($this->studentId) {
            $student = Student::findOrFail($this->studentId);
            $student->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
            $student->update([
                'admission_number' => $validated['admissionNumber'],
                'school_class_id' => $validated['schoolClassId'] ?: null,
                'section_id' => $validated['sectionId'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'date_of_birth' => $validated['dateOfBirth'] ?: null,
            ]);
        } else {
            $password = Str::password(12);

            $user = User::create([
                'school_id' => $schoolId,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $password,
                'status' => 'active',
            ]);
            $user->assignRole('student');

            Student::create([
                'user_id' => $user->id,
                'school_id' => $schoolId,
                'school_class_id' => $validated['schoolClassId'] ?: null,
                'section_id' => $validated['sectionId'] ?: null,
                'admission_number' => $validated['admissionNumber'],
                'admission_date' => now(),
                'gender' => $validated['gender'] ?: null,
                'date_of_birth' => $validated['dateOfBirth'] ?: null,
                'status' => 'active',
            ]);

            $this->generatedPassword = $password;
        }

        $this->showModal = false;
        $this->resetForm(keepGeneratedPassword: true);
    }

    public function delete(int $id): void
    {
        $student = Student::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $user = $student->user;
        $student->delete();
        $user?->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function dismissGeneratedPassword(): void
    {
        $this->generatedPassword = null;
    }

    private function resetForm(bool $keepGeneratedPassword = false): void
    {
        $this->reset(['studentId', 'userId', 'name', 'email', 'phone', 'admissionNumber', 'schoolClassId', 'sectionId', 'dateOfBirth']);
        $this->gender = 'male';
        if (! $keepGeneratedPassword) {
            $this->generatedPassword = null;
        }
        $this->resetErrorBag();
    }
}
