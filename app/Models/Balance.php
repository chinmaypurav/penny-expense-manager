<?php

namespace App\Models;

use App\Enums\RecordType;
use App\Observers\BalanceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(BalanceObserver::class)]
class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'balance',
        'recorded_until',
        'record_type',
    ];

    protected function casts(): array
    {
        return [
            'recorded_until' => 'date:Y-m-d',
            'record_type' => RecordType::class,
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
