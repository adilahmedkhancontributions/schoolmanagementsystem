<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'payable_type',
        'payable_id',
        'period',
        'basic_salary',
        'house_allowance',
        'transport_allowance',
        'other_allowance',
        'bonus',
        'deduction',
        'net_salary',
        'status',
        'paid_at',
        'payment_method',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'house_allowance' => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'paid_at' => 'date',
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

    public function periodLabel(): string
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->period)->format('F Y');
    }
}
