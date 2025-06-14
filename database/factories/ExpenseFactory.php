<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Person;
use App\Models\User;
use Database\Factories\Concerns\Timeable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExpenseFactory extends Factory
{
    use Timeable;

    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'description' => fake()->text(),
            'transacted_at' => fake()->dateTimeBetween('-1 years', 'now'),
            'amount' => fake()->randomFloat(2),

            'user_id' => User::factory(),
            'person_id' => Person::factory(),
            'account_id' => Account::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
