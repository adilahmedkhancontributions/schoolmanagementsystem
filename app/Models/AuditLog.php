<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'auditable_type',
        'auditable_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function modelLabel(): string
    {
        return match ($this->auditable_type) {
            Student::class => 'Student',
            Teacher::class => 'Teacher',
            FeeInvoice::class => 'Fee Invoice',
            FeePayment::class => 'Fee Payment',
            ExamResult::class => 'Exam Result',
            Attendance::class => 'Attendance',
            default => class_basename($this->auditable_type),
        };
    }
}
