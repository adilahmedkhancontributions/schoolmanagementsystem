<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeInvoice extends Model
{
    use HasFactory;
    use \App\Support\Auditable;

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_structure_id',
        'title',
        'amount',
        'paid_amount',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(FeeDiscount::class);
    }

    public function discountTotal(): string
    {
        return $this->discounts->reduce(
            fn ($carry, $discount) => bcadd($carry, $discount->amountFor((string) $this->amount), 2),
            '0.00'
        );
    }

    public function netAmount(): string
    {
        return bcsub((string) $this->amount, $this->discountTotal(), 2);
    }

    public function balance(): string
    {
        return bcsub($this->netAmount(), (string) $this->paid_amount, 2);
    }

    public function refreshStatus(): void
    {
        $net = $this->netAmount();

        $this->status = match (true) {
            bccomp((string) $this->paid_amount, $net, 2) >= 0 => 'paid',
            bccomp((string) $this->paid_amount, '0', 2) > 0 => 'partial',
            default => 'unpaid',
        };
        $this->save();
    }
}
