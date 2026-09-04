<?php

namespace App\Livewire\SchoolAdmin\Students;

use App\Models\Campus;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AccountCreated;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Document;

#[Layout('layouts.dashboard')]
class Manage extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    public ?int $filterClassId = null;

    public ?int $filterSectionId = null;

    public ?int $filterCampusId = null;

    public bool $showModal = false;
    public bool $showDocumentsModal = false;

    public ?int $studentId = null;
    public ?int $docStudentId = null;

    public ?int $userId = null;
    public $documents = [];
    public ?string $documentTitle = null;
    public $documentFile;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $admissionNumber = '';

    public ?int $schoolClassId = null;

    public ?int $sectionId = null;

    public ?int $campusId = null;

    public string $gender = 'male';

    public string $dateOfBirth = '';

    public string $bloodGroup = '';

    public string $nationality = '';

    public string $religion = '';

    public string $emergencyContactName = '';

    public string $emergencyContactPhone = '';

    public string $emergencyContactRelation = '';

    public string $medicalNotes = '';

    public string $notes = '';

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

        $students = Student::with(['user', 'schoolClass', 'section', 'campus'])
            ->where('school_id', $schoolId)
            ->when($this->filterCampusId, fn ($q) => $q->where('campus_id', $this->filterCampusId))
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
            'campuses' => Campus::where('school_id', $schoolId)->orderBy('name')->get(),
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
        $this->campusId = $student->campus_id;
        $this->gender = (string) $student->gender;
        $this->dateOfBirth = optional($student->date_of_birth)->format('Y-m-d') ?? '';
        $this->bloodGroup = (string) $student->blood_group;
        $this->nationality = (string) $student->nationality;
        $this->religion = (string) $student->religion;
        $this->emergencyContactName = (string) $student->emergency_contact_name;
        $this->emergencyContactPhone = (string) $student->emergency_contact_phone;
        $this->emergencyContactRelation = (string) $student->emergency_contact_relation;
        $this->medicalNotes = (string) $student->medical_notes;
        $this->notes = (string) $student->notes;
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
            'campusId' => 'nullable|exists:campuses,id',
            'gender' => 'nullable|in:male,female,other',
            'dateOfBirth' => 'nullable|date',
            'bloodGroup' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:80',
            'religion' => 'nullable|string|max:80',
            'emergencyContactName' => 'nullable|string|max:120',
            'emergencyContactPhone' => 'nullable|string|max:40',
            'emergencyContactRelation' => 'nullable|string|max:50',
            'medicalNotes' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $schoolId = auth()->user()->school_id;

        if ($this->studentId) {
            $student = Student::where('school_id', $schoolId)->findOrFail($this->studentId);
            $student->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
            $student->update([
                'admission_number' => $validated['admissionNumber'],
                'school_class_id' => $validated['schoolClassId'] ?: null,
                'section_id' => $validated['sectionId'] ?: null,
                'campus_id' => $validated['campusId'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'date_of_birth' => $validated['dateOfBirth'] ?: null,
                'blood_group' => $validated['bloodGroup'] ?: null,
                'nationality' => $validated['nationality'] ?: null,
                'religion' => $validated['religion'] ?: null,
                'emergency_contact_name' => $validated['emergencyContactName'] ?: null,
                'emergency_contact_phone' => $validated['emergencyContactPhone'] ?: null,
                'emergency_contact_relation' => $validated['emergencyContactRelation'] ?: null,
                'medical_notes' => $validated['medicalNotes'] ?: null,
                'notes' => $validated['notes'] ?: null,
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
                'campus_id' => $validated['campusId'] ?: null,
                'admission_number' => $validated['admissionNumber'],
                'admission_date' => now(),
                'gender' => $validated['gender'] ?: null,
                'date_of_birth' => $validated['dateOfBirth'] ?: null,
                'blood_group' => $validated['bloodGroup'] ?: null,
                'nationality' => $validated['nationality'] ?: null,
                'religion' => $validated['religion'] ?: null,
                'emergency_contact_name' => $validated['emergencyContactName'] ?: null,
                'emergency_contact_phone' => $validated['emergencyContactPhone'] ?: null,
                'emergency_contact_relation' => $validated['emergencyContactRelation'] ?: null,
                'medical_notes' => $validated['medicalNotes'] ?: null,
                'notes' => $validated['notes'] ?: null,
                'status' => 'active',
            ]);

            Notification::send($user, new AccountCreated('Student', $validated['email'], $password));

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

    public function openDocuments(int $id): void
    {
        Student::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->docStudentId = $id;
        $this->loadDocuments();
        $this->showDocumentsModal = true;
    }

    public function loadDocuments(): void
    {
        $this->documents = Document::where('school_id', auth()->user()->school_id)
            ->where('documentable_type', Student::class)
            ->where('documentable_id', $this->docStudentId)
            ->get();
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'documentTitle' => 'required|string|max:255',
            'documentFile' => 'required|file|max:10240',
        ]);

        Student::where('school_id', auth()->user()->school_id)->findOrFail($this->docStudentId);

        $path = $this->documentFile->store('documents', 'public');

        Document::create([
            'school_id' => auth()->user()->school_id,
            'documentable_type' => Student::class,
            'documentable_id' => $this->docStudentId,
            'title' => $this->documentTitle,
            'file_path' => $path,
            'file_type' => $this->documentFile->getClientOriginalExtension(),
        ]);

        $this->reset(['documentTitle', 'documentFile']);
        $this->loadDocuments();
    }

    public function deleteDocument(int $id): void
    {
        $doc = Document::where('school_id', auth()->user()->school_id)->findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
        $doc->delete();
        $this->loadDocuments();
    }

    public function closeDocumentsModal(): void
    {
        $this->showDocumentsModal = false;
        $this->reset(['docStudentId', 'documents', 'documentTitle', 'documentFile']);
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
        $this->reset(['studentId', 'userId', 'name', 'email', 'phone', 'admissionNumber', 'schoolClassId', 'sectionId', 'campusId', 'dateOfBirth', 'bloodGroup', 'nationality', 'religion', 'emergencyContactName', 'emergencyContactPhone', 'emergencyContactRelation', 'medicalNotes', 'notes', 'docStudentId', 'documents', 'documentTitle', 'documentFile']);
        $this->gender = 'male';
        if (! $keepGeneratedPassword) {
            $this->generatedPassword = null;
        }
        $this->resetErrorBag();
    }
}
