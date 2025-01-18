<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nick_name',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'person_id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'person_id');
    }
}
