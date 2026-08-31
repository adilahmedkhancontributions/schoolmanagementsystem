<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    use HasFactory;
    use \App\Support\Auditable;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'school_id',
        'campus_id',
        'employee_id',
        'designation',
        'department',
        'joining_date',
        'employment_type',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function salaryStructure()
    {
        return $this->morphOne(SalaryStructure::class, 'payable');
    }

    public function payslips()
    {
        return $this->morphMany(Payslip::class, 'payable');
    }
}
