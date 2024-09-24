<?php

namespace App\Models;

use App\Enums\Frequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class RecurringTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'creditor_id',
        'debtor_id',
        'description',
        'amount',
        'next_transaction_at',
        'frequency',
        'remaining_recurrences',
    ];

    protected function casts(): array
    {
        return [
            'next_transaction_at' => 'date:Y-m-d',
            'frequency' => Frequency::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creditor(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'creditor_id');
    }

    public function debtor(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debtor_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
