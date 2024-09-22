<?php

namespace App\Models;

use App\Enums\Frequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'person_id',
        'account_id',
        'category_id',
        'description',
        'amount',
        'next_transaction_at',
        'frequency',
        'remaining_recurrences',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'next_transaction_at' => 'date:Y-m-d',
            'frequency' => Frequency::class,
        ];
    }
}
