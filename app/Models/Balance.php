<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    use HasFactory;

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
            'recorded_until' => 'date:Y-m-d',
            'is_initial_record' => 'boolean',
        ];
    }
}
