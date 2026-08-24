<div>
    @php
        $statusColors = [
            'inquiry' => 'bg-slate-100 text-slate-700',
            'interview_scheduled' => 'bg-sky-50 text-sky-700',
            'test_scheduled' => 'bg-amber-50 text-amber-700',
            'offered' => 'bg-indigo-50 text-indigo-700',
            'enrolled' => 'bg-emerald-50 text-emerald-700',
            'rejected' => 'bg-rose-50 text-rose-700',
            'withdrawn' => 'bg-slate-100 text-slate-500',
        ];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-900">Admissions</h1>
            <p class="text-sm text-slate-500 mt-1">Track applicants from inquiry through interview, test and enrollment.</p>
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <i class="fa-solid fa-plus"></i> New Inquiry
        </button>
    </div>

    @if ($generatedPassword)
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 flex items-start justify-between gap-4">
            <div class="text-sm text-emerald-800">
                Applicant enrolled as a student. Temporary password:
                <code class="font-mono font-semibold bg-white/70 px-1.5 py-0.5 rounded">{{ $generatedPassword }}</code>
                — share it securely, they should change it after first login.
            </div>
            <button type="button" wire:click="dismissGeneratedPassword" class="text-emerald-700 min-h-touch min-w-touch">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @error('enroll')
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">{{ $message }}</div>
    @enderror

    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search by name, father, phone or email..."
               class="w-full sm:w-72 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">

        <select wire:model.live="filterStatus" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">All statuses</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        <select wire:model.live="filterClassId" class="min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <option value="">All classes</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
        @forelse ($admissions as $admission)
            <div class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-slate-800 truncate">{{ $admission->applicant_name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $admission->phone ?? $admission->email ?? '—' }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{ $statusColors[$admission->status] }}">
                        {{ $statuses[$admission->status] }}
                    </span>
                </div>
                <dl class="mt-3 grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <dt class="text-slate-400">Class applied for</dt>
                        <dd class="text-slate-700 font-medium mt-0.5">{{ $admission->schoolClass?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Source</dt>
                        <dd class="text-slate-700 font-medium mt-0.5">{{ $admission->source ?? '—' }}</dd>
                    </div>
                </dl>
                <div class="mt-3 flex items-center gap-1 flex-wrap">
                    <button type="button" wire:click="openDocuments({{ $admission->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-emerald-600">
                        <i class="fa-solid fa-file-lines"></i>
                    </button>
                    <button type="button" wire:click="openEdit({{ $admission->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    @if (! in_array($admission->status, ['enrolled', 'rejected', 'withdrawn']))
                        <button type="button" wire:click="openStage({{ $admission->id }})" class="btn-secondary !min-h-touch !py-1 !px-3 text-xs">
                            Advance
                        </button>
                    @endif
                    @if ($admission->status === 'offered')
                        <button type="button" wire:click="enroll({{ $admission->id }})" wire:confirm="Enroll this applicant as a student?" class="btn-primary !min-h-touch !py-1 !px-3 text-xs">
                            Enroll
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-500 text-sm">No applications yet.</div>
        @endforelse
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                    <th class="py-3 px-4">Applicant</th>
                    <th class="py-3 px-4">Class applied for</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($admissions as $admission)
                    <tr>
                        <td class="py-3 px-4 font-medium text-slate-800">
                            {{ $admission->applicant_name }}
                            @if ($admission->father_name)
                                <span class="block text-xs text-slate-400">S/D of {{ $admission->father_name }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $admission->schoolClass?->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-slate-600">{{ $admission->phone ?? $admission->email ?? '—' }}</td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{ $statusColors[$admission->status] }}">
                                {{ $statuses[$admission->status] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <button type="button" wire:click="openDocuments({{ $admission->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-emerald-600">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                            <button type="button" wire:click="openEdit({{ $admission->id }})" class="min-h-touch min-w-touch text-slate-500 hover:text-indigo-600">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @if (! in_array($admission->status, ['enrolled', 'rejected', 'withdrawn']))
                                <button type="button" wire:click="openStage({{ $admission->id }})" class="btn-secondary !min-h-touch !py-1 !px-3 text-xs">
                                    Advance
                                </button>
                                <button type="button" wire:click="withdraw({{ $admission->id }})" wire:confirm="Mark this application as withdrawn?" class="min-h-touch min-w-touch text-slate-500 hover:text-rose-600">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            @endif
                            @if ($admission->status === 'offered')
                                <button type="button" wire:click="enroll({{ $admission->id }})" wire:confirm="Enroll this applicant as a student?" class="btn-primary !min-h-touch !py-1 !px-3 text-xs">
                                    Enroll
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">No applications yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $admissions->links() }}</div>

    <x-crud-modal :show="$showModal" wireClose="closeModal" :title="$admissionId ? 'Edit Application' : 'New Inquiry'" maxWidth="xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-floating-input label="Applicant name" name="applicantName" wire:model="applicantName" />
                <x-floating-input label="Father's name" name="fatherName" wire:model="fatherName" />
                <x-floating-input label="Phone" name="phone" wire:model="phone" />
                <x-floating-input label="Email" name="email" type="email" wire:model="email" />
                <x-floating-input label="Date of birth" name="dateOfBirth" type="date" wire:model="dateOfBirth" />
                <x-floating-select label="Gender" name="gender" wire:model="gender">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </x-floating-select>
                <x-floating-select label="Class applying for" name="schoolClassId" wire:model="schoolClassId">
                    <option value="">— None —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </x-floating-select>
                <x-floating-input label="Source (referral, walk-in, website...)" name="source" wire:model="source" />
                <x-floating-input label="Address" name="address" wire:model="address" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal :show="$showStageModal" wireClose="closeStageModal" title="Move Application Forward" maxWidth="lg">
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Interview</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-floating-input label="Interview date/time" name="interviewDate" type="datetime-local" wire:model="interviewDate" />
                    <x-floating-input label="Interview notes" name="interviewNotes" wire:model="interviewNotes" />
                </div>
                <button type="button" wire:click="scheduleInterview" class="btn-secondary mt-2">Save Interview</button>
            </div>

            <div class="border-t pt-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Entry Test</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-floating-input label="Test date/time" name="testDate" type="datetime-local" wire:model="testDate" />
                </div>
                <button type="button" wire:click="scheduleTest" class="btn-secondary mt-2">Schedule Test</button>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <x-floating-input label="Test score (%)" name="testScore" type="number" step="0.01" wire:model="testScore" />
                    <x-floating-input label="Test notes" name="testNotes" wire:model="testNotes" />
                </div>
                <button type="button" wire:click="recordTestResult" class="btn-secondary mt-2">Save Test Result</button>
            </div>

            <div class="border-t pt-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Decision</h3>
                <x-floating-input label="Decision notes" name="decisionNotes" wire:model="decisionNotes" />
                <div class="flex gap-2 mt-2">
                    <button type="button" wire:click="makeOffer" class="btn-primary">Make Offer</button>
                    <button type="button" wire:click="reject" class="rounded-lg border border-rose-300 text-rose-600 px-4 py-2 text-sm font-medium hover:bg-rose-50">Reject</button>
                </div>
            </div>
        </div>
    </x-crud-modal>

    <x-crud-modal :show="$showDocumentsModal" wireClose="closeDocumentsModal" title="Documents" maxWidth="lg">
        <div class="space-y-4">
            <ul class="divide-y divide-slate-100">
                @forelse ($documents as $doc)
                    <li class="py-2 flex justify-between items-center">
                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">
                            {{ $doc->title }}
                        </a>
                        <button wire:click="deleteDocument({{ $doc->id }})" class="text-rose-500 text-xs">Delete</button>
                    </li>
                @empty
                    <li class="py-2 text-sm text-slate-500">No documents uploaded.</li>
                @endforelse
            </ul>

            <form wire:submit="uploadDocument" class="border-t pt-4">
                <x-floating-input label="Title" wire:model="documentTitle" />
                <input type="file" wire:model="documentFile" class="mt-2 block w-full text-sm">
                <button type="submit" class="btn-primary mt-3 w-full">Upload</button>
            </form>
        </div>
    </x-crud-modal>
</div>
