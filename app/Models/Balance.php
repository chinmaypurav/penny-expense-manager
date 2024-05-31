<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    protected $fillable = [
        'account_id',
        'balance',
        'recorded_until',
        'is_initial_record',
        'record_type',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected function casts(): array
    {
        return [
            'recorded_until' => 'date',
            'is_initial_record' => 'boolean',
        ];
    }
}
