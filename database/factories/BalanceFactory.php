<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Balance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BalanceFactory extends Factory
{
    protected $model = Balance::class;

    public function definition(): array
    {
        return [
            'balance' => fake()->randomFloat(2),
            'recorded_until' => fake()->date(),
            'is_initial_record' => false,
            'record_type' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'account_id' => Account::factory(),
        ];
    }

    public function initialRecord(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_initial_record' => true,
        ]);
    }
}
