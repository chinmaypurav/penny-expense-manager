<?php

namespace App\Models;

use App\Filament\Concerns\Transactional;
use App\Observers\TransferObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy(TransferObserver::class)]
class Transfer extends Model
{
    use HasFactory, Transactional;

    protected $fillable = [
        'user_id',
        'creditor_id',
        'debtor_id',
        'description',
        'transacted_at',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'transacted_at' => 'datetime',
            'data' => 'array',
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
