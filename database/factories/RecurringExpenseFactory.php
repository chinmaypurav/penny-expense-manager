<?php

namespace Database\Factories;

use App\Enums\Frequency;
use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RecurringExpenseFactory extends Factory
{
    protected $model = RecurringExpense::class;

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
            'account_id' => Account::factory(),
            'person_id' => Person::factory(),
            'category_id' => Category::factory(),
        ];
    }

    public function lifetime(): static
    {
        return $this->state(fn (array $attributes) => [
            'remaining_recurrences' => null,
        ]);
    }

    public function withoutAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_id' => null,
        ]);
    }
}
