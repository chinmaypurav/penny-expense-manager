<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransferFactory extends Factory
{
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

    public function today(): self
    {
        return $this->state(fn (array $attributes) => [
            'transacted_at' => Carbon::now(),
        ]);
    }

    public function tomorrow(): self
    {
        return $this->state(fn (array $attributes) => [
            'transacted_at' => Carbon::now()->addDay(),
        ]);
    }

    public function yesterday(): self
    {
        return $this->state(fn (array $attributes) => [
            'transacted_at' => Carbon::now()->subDay(),
        ]);
    }
}
