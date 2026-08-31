<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'payable_type',
        'payable_id',
        'basic_salary',
        'house_allowance',
        'transport_allowance',
        'other_allowance',
        'deduction',
        'effective_from',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'house_allowance' => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'deduction' => 'decimal:2',
            'effective_from' => 'date',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function gross(): float
    {
        return (float) $this->basic_salary
            + (float) $this->house_allowance
            + (float) $this->transport_allowance
            + (float) $this->other_allowance;
    }

    public function net(): float
    {
        return $this->gross() - (float) $this->deduction;
    }
}
