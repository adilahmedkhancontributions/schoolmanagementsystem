<div>
    <div class="relative overflow-hidden rounded-2xl brand-gradient text-white p-6 mb-6">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.18),transparent_55%)]"></div>
        <div class="relative flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-white/15 flex items-center justify-center">
                <i class="fa-solid fa-book-open text-lg"></i>
            </div>
            <div>
                <h1 class="font-heading text-xl sm:text-2xl font-bold">Homework</h1>
                <p class="text-sm text-white/80 mt-0.5">Assignments, due dates, and submissions.</p>
            </div>
        </div>
    </div>

    @if ($children->isNotEmpty())
        <div class="mb-4">
            <select wire:model.live="studentId" class="w-full sm:w-64 min-h-touch rounded-lg border border-slate-300 px-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
                @foreach ($children as $child)
                    <option value="{{ $child->id }}">{{ $child->user->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($studentId)
        @php
            $statusBadge = function ($submission, $homework) {
                if (! $submission) {
                    return $homework->due_date->isPast()
                        ? ['label' => 'Overdue', 'class' => 'bg-rose-50 text-rose-700']
                        : ['label' => 'Pending', 'class' => 'bg-slate-100 text-slate-500'];
                }
                return match ($submission->status()) {
                    'graded' => ['label' => 'Graded', 'class' => 'bg-emerald-50 text-emerald-700'],
                    'submitted' => ['label' => 'Submitted', 'class' => 'bg-sky-50 text-sky-700'],
                    default => ['label' => 'Pending', 'class' => 'bg-slate-100 text-slate-500'],
                };
            };
        @endphp

        <!-- Mobile card list -->
        <div class="sm:hidden space-y-3">
            @forelse ($homeworks as $homework)
                @php($submission = $submissions->get($homework->id))
                @php($badge = $statusBadge($submission, $homework))
                <div class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-800 truncate">{{ $homework->title }}</p>
                            <p class="text-xs text-slate-500">{{ $homework->subject->name }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500"><i class="fa-solid fa-calendar-days mr-1"></i> Due {{ $homework->due_date->format('d M Y') }}</p>
                    @if ($homework->description)
                        <p class="mt-2 text-xs text-slate-600">{{ $homework->description }}</p>
                    @endif
                    @if ($homework->attachmentUrl())
                        <a href="{{ $homework->attachmentUrl() }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline">
                            <i class="fa-solid fa-paperclip"></i> View attachment
                        </a>
                    @endif
                    @if ($submission?->marks_obtained !== null)
                        <p class="mt-2 text-xs text-slate-600">Marks: {{ number_format($submission->marks_obtained, 2) }}{{ $homework->max_marks !== null ? ' / '.number_format($homework->max_marks, 2) : '' }}</p>
                    @endif
                    @if ($submission?->feedback)
                        <p class="mt-1 text-xs text-slate-500">Feedback: {{ $submission->feedback }}</p>
                    @endif
                    <button type="button" wire:click="openSubmit({{ $homework->id }})" class="btn-secondary mt-3 w-full">
                        <i class="fa-solid fa-upload"></i> {{ $submission ? 'Update Submission' : 'Submit' }}
                    </button>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-500 text-sm">No homework assigned yet.</div>
            @endforelse
        </div>

        <!-- Desktop table -->
        <div class="hidden sm:block card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Due Date</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Marks</th>
                        <th class="py-3 px-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($homeworks as $homework)
                        @php($submission = $submissions->get($homework->id))
                        @php($badge = $statusBadge($submission, $homework))
                        <tr>
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ $homework->title }}
                                @if ($homework->attachmentUrl())
                                    <a href="{{ $homework->attachmentUrl() }}" target="_blank" class="ml-1 text-indigo-600 hover:underline" title="View attachment">
                                        <i class="fa-solid fa-paperclip"></i>
                                    </a>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $homework->subject->name }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $homework->due_date->format('d M Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $submission?->marks_obtained !== null ? number_format($submission->marks_obtained, 2) : '—' }}{{ $submission?->marks_obtained !== null && $homework->max_marks !== null ? ' / '.number_format($homework->max_marks, 2) : '' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button type="button" wire:click="openSubmit({{ $homework->id }})" class="btn-secondary">
                                    <i class="fa-solid fa-upload"></i> {{ $submission ? 'Update' : 'Submit' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-500">No homework assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-slate-500">No student profile linked to this account yet.</p>
    @endif

    <x-crud-modal :show="$showSubmitModal" wireClose="closeSubmitModal" :title="'Submit — '.($activeHomework->title ?? '')">
        <form wire:submit="submit" class="space-y-4">
            <div>
                <textarea wire:model="submissionText" rows="4" placeholder="Write your answer or notes (optional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"></textarea>
                @error('submissionText') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Attach a file (optional)</label>
                <input type="file" wire:model="submissionFile" class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                <div wire:loading wire:target="submissionFile" class="text-xs text-slate-400 mt-1">Uploading…</div>
                @error('submissionFile') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" wire:click="closeSubmitModal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Submit</button>
            </div>
        </form>
    </x-crud-modal>
</div>
