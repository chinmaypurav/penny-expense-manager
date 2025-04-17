<?php

namespace Database\Factories;

use App\Enums\Frequency;
use App\Models\Account;
use App\Models\RecurringTransfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RecurringTransferFactory extends Factory
{
    protected $model = RecurringTransfer::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'description' => fake()->text(),
            'amount' => fake()->randomFloat(2),
            'next_transaction_at' => fake()->dateTimeBetween('tomorrow', '+1 years'),
            'frequency' => fake()->randomElement(Frequency::cases()),
            'remaining_recurrences' => fake()->randomNumber(),

            'user_id' => User::factory(),
            'creditor_id' => Account::factory(),
            'debtor_id' => Account::factory(),
        ];
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'next_transaction_at' => Carbon::today(),
        ]);
    }

    public function lifetime(): static
    {
        return $this->state(fn (array $attributes) => [
            'remaining_recurrences' => null,
        ]);
    }
}
