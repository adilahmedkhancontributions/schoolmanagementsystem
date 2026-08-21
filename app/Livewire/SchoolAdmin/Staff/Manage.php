<?php

namespace App\Livewire\SchoolAdmin\Staff;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Contracts\View\View;
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

    public ?int $staffId = null;
    public ?int $docStaffId = null;

    public ?int $userId = null;
    public $documents = [];
    public ?string $documentTitle = null;
    public $documentFile;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $employeeId = '';

    public string $designation = '';

    public string $department = '';

    public string $employmentType = 'full_time';

    public ?string $generatedPassword = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $schoolId = auth()->user()->school_id;

        $staffMembers = Staff::with('user')
            ->where('school_id', $schoolId)
            ->whereHas('user', fn ($q) => $q->when(
                $this->search,
                fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.school-admin.staff.manage', [
            'staffMembers' => $staffMembers,
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $staff = Staff::with('user')->where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->staffId = $staff->id;
        $this->userId = $staff->user_id;
        $this->name = $staff->user->name;
        $this->email = $staff->user->email;
        $this->phone = (string) $staff->user->phone;
        $this->employeeId = $staff->employee_id;
        $this->designation = $staff->designation;
        $this->department = (string) $staff->department;
        $this->employmentType = $staff->employment_type;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'phone' => 'nullable|string|max:30',
            'employeeId' => ['required', 'string', 'max:50', Rule::unique('staff', 'employee_id')->ignore($this->staffId)],
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'employmentType' => 'required|in:full_time,part_time,contract',
        ]);

        $schoolId = auth()->user()->school_id;

        if ($this->staffId) {
            $staff = Staff::findOrFail($this->staffId);
            $staff->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
            $staff->update([
                'employee_id' => $validated['employeeId'],
                'designation' => $validated['designation'],
                'department' => $validated['department'],
                'employment_type' => $validated['employmentType'],
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
            $user->assignRole('staff');

            Staff::create([
                'user_id' => $user->id,
                'school_id' => $schoolId,
                'employee_id' => $validated['employeeId'],
                'designation' => $validated['designation'],
                'department' => $validated['department'],
                'employment_type' => $validated['employmentType'],
                'joining_date' => now(),
            ]);

            $this->generatedPassword = $password;
        }

        $this->showModal = false;
        $this->resetForm(keepGeneratedPassword: true);
    }

    public function delete(int $id): void
    {
        $staff = Staff::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $user = $staff->user;
        $staff->delete();
        $user?->delete();
    }

    public function openDocuments(int $id): void
    {
        $this->docStaffId = $id;
        $this->loadDocuments();
        $this->showDocumentsModal = true;
    }

    public function loadDocuments(): void
    {
        $this->documents = Document::where('documentable_type', Staff::class)
            ->where('documentable_id', $this->docStaffId)
            ->get();
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'documentTitle' => 'required|string|max:255',
            'documentFile' => 'required|file|max:10240',
        ]);

        $path = $this->documentFile->store('documents', 'public');

        Document::create([
            'school_id' => auth()->user()->school_id,
            'documentable_type' => Staff::class,
            'documentable_id' => $this->docStaffId,
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
        $this->reset(['docStaffId', 'documents', 'documentTitle', 'documentFile']);
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
        $this->reset(['staffId', 'userId', 'name', 'email', 'phone', 'employeeId', 'designation', 'department', 'docStaffId', 'documents', 'documentTitle', 'documentFile']);
        $this->employmentType = 'full_time';
        if (! $keepGeneratedPassword) {
            $this->generatedPassword = null;
        }
        $this->resetErrorBag();
    }
}
