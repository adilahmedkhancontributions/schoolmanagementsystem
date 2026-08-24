<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkSubmission extends Model
{
    use HasFactory;
    use \App\Support\Auditable;

    protected $fillable = [
        'homework_id',
        'student_id',
        'submission_text',
        'file_path',
        'submitted_at',
        'marks_obtained',
        'feedback',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'marks_obtained' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function status(): string
    {
        if ($this->marks_obtained !== null) {
            return 'graded';
        }

        return $this->submitted_at ? 'submitted' : 'pending';
    }

    public function fileUrl(): ?string
    {
        return $this->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path) : null;
    }
}
