<?php

namespace App\Livewire\SchoolAdmin\Teachers;

use App\Models\Campus;
use App\Models\Teacher;
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

    public bool $showModal = false;
    public bool $showDocumentsModal = false;

    public ?int $teacherId = null;
    public ?int $docTeacherId = null;

    public ?int $userId = null;
    public $documents = [];
    public ?string $documentTitle = null;
    public $documentFile;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $employeeId = '';

    public string $qualification = '';

    public string $specialization = '';

    public string $employmentType = 'full_time';

    public ?int $campusId = null;

    public ?string $generatedPassword = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $teachers = Teacher::with(['user', 'campus'])
            ->where('school_id', $schoolId)
            ->whereHas('user', fn ($q) => $q->when(
                $this->search,
                fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.school-admin.teachers.manage', [
            'teachers' => $teachers,
            'campuses' => Campus::where('school_id', $schoolId)->orderBy('name')->get(),
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $teacher = Teacher::with('user')->where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->teacherId = $teacher->id;
        $this->userId = $teacher->user_id;
        $this->name = $teacher->user->name;
        $this->email = $teacher->user->email;
        $this->phone = (string) $teacher->user->phone;
        $this->employeeId = $teacher->employee_id;
        $this->qualification = (string) $teacher->qualification;
        $this->specialization = (string) $teacher->specialization;
        $this->employmentType = $teacher->employment_type;
        $this->campusId = $teacher->campus_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'phone' => 'nullable|string|max:30',
            'employeeId' => ['required', 'string', 'max:50', Rule::unique('teachers', 'employee_id')->ignore($this->teacherId)],
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'employmentType' => 'required|in:full_time,part_time,contract',
            'campusId' => 'nullable|exists:campuses,id',
        ]);

        $schoolId = auth()->user()->school_id;

        if ($this->teacherId) {
            $teacher = Teacher::where('school_id', $schoolId)->findOrFail($this->teacherId);
            $teacher->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
            $teacher->update([
                'employee_id' => $validated['employeeId'],
                'qualification' => $validated['qualification'],
                'specialization' => $validated['specialization'],
                'employment_type' => $validated['employmentType'],
                'campus_id' => $validated['campusId'] ?: null,
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
            $user->assignRole('teacher');

            Teacher::create([
                'user_id' => $user->id,
                'school_id' => $schoolId,
                'employee_id' => $validated['employeeId'],
                'qualification' => $validated['qualification'],
                'specialization' => $validated['specialization'],
                'employment_type' => $validated['employmentType'],
                'campus_id' => $validated['campusId'] ?: null,
                'joining_date' => now(),
            ]);

            Notification::send($user, new AccountCreated('Teacher', $validated['email'], $password));

            $this->generatedPassword = $password;
        }

        $this->showModal = false;
        $this->resetForm(keepGeneratedPassword: true);
    }

    public function delete(int $id): void
    {
        $teacher = Teacher::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $user = $teacher->user;
        $teacher->delete();
        $user?->delete();
    }

    public function openDocuments(int $id): void
    {
        Teacher::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->docTeacherId = $id;
        $this->loadDocuments();
        $this->showDocumentsModal = true;
    }

    public function loadDocuments(): void
    {
        $this->documents = Document::where('school_id', auth()->user()->school_id)
            ->where('documentable_type', Teacher::class)
            ->where('documentable_id', $this->docTeacherId)
            ->get();
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'documentTitle' => 'required|string|max:255',
            'documentFile' => 'required|file|max:10240',
        ]);

        Teacher::where('school_id', auth()->user()->school_id)->findOrFail($this->docTeacherId);

        $path = $this->documentFile->store('documents', 'public');

        Document::create([
            'school_id' => auth()->user()->school_id,
            'documentable_type' => Teacher::class,
            'documentable_id' => $this->docTeacherId,
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
        $this->reset(['docTeacherId', 'documents', 'documentTitle', 'documentFile']);
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
        $this->reset(['teacherId', 'userId', 'name', 'email', 'phone', 'employeeId', 'qualification', 'specialization', 'campusId', 'docTeacherId', 'documents', 'documentTitle', 'documentFile']);
        $this->employmentType = 'full_time';
        if (! $keepGeneratedPassword) {
            $this->generatedPassword = null;
        }
        $this->resetErrorBag();
    }
}
