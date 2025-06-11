<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Database\Factories\Concerns\Timeable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransferFactory extends Factory
{
    use Timeable;

    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'description' => fake()->text(),
            'amount' => fake()->randomFloat(2),
            'transacted_at' => fake()->dateTimeBetween('-1 years', 'now'),

            'user_id' => User::factory(),
            'creditor_id' => Account::factory(),
            'debtor_id' => Account::factory(),
        ];
    }
}
