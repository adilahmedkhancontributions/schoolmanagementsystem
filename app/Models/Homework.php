<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homework extends Model
{
    use HasFactory;
    use \App\Support\Auditable;

    protected $fillable = [
        'school_id',
        'school_class_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'due_date',
        'max_marks',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'max_marks' => 'decimal:2',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->attachment_path) : null;
    }
}
