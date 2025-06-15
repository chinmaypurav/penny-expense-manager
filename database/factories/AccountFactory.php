<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => fake()->randomElement(['ICICI', 'AXIS', 'HDFC', 'IDFC']),
            'identifier' => fake()->numerify(),
            'account_type' => fake()->randomElement(AccountType::cases()),
            'initial_balance' => fake()->randomFloat(2),
            'initial_date' => fake()->dateTimeBetween('-1 years'),
            'current_balance' => fake()->randomFloat(2),

            'user_id' => User::factory(),
        ];
    }

    public function tomorrow(): self
    {
        return $this->state(fn (array $attributes) => [
            'initial_date' => Carbon::tomorrow(),
        ]);
    }

    public function today(): self
    {
        return $this->state(fn (array $attributes) => [
            'initial_date' => Carbon::today(),
        ]);
    }

    public function yesterday(): self
    {
        return $this->state(fn (array $attributes) => [
            'initial_date' => Carbon::yesterday(),
        ]);
    }
}
