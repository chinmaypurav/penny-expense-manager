<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'financial_year_id',
        'salary',
        'dividend',
        'rent',
        'interest',
        'ltcg',
        'stcg',
        'speculative_income',
        'other_income',
        'tax_paid',
        'data',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
