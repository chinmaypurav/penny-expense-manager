<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Observers\AccountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AccountObserver::class)]
class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'identifier',
        'account_type',
        'current_balance',
        'initial_date',
        'initial_balance',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'initial_date' => 'date:Y-m-d',
            'data' => 'array',
        ];
    }

    protected function label(): Attribute
    {
        return Attribute::get(fn () => "{$this->account_type->getShortCode()} | {$this->name} {$this->identifier}");
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

    public function previousBalance(): HasOne
    {
        return $this->hasOne(Balance::class, 'account_id')
            ->orderByDesc('recorded_until');
    }
}
