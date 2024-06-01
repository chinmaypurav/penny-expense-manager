<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Observers\AccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy(AccountObserver::class)]
class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'account_type',
        'current_balance',
    ];

    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'account_id');
    }

    public function creditTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'creditor_id');
    }

    public function debitTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'debtor_id');
    }

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class, 'account_id');
    }

    public function initialBalance(): HasOne
    {
        return $this->hasOne(Balance::class, 'account_id')
            ->where('is_initial_record', true);
    }
}
