<?php

namespace Database\Factories;

use App\Models\AnnualStatement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AnnualStatementFactory extends Factory
{
    protected $model = AnnualStatement::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'data' => [],
            'financial_year' => fake()->year(),
            'salary' => fake()->randomFloat(2),
            'dividend' => fake()->randomFloat(2),
            'interest' => fake()->randomFloat(2),
            'ltcg' => fake()->randomFloat(2),
            'stcg' => fake()->randomFloat(2),
            'other_income' => fake()->randomFloat(2),
            'tax_paid' => fake()->randomFloat(2),

            'user_id' => User::factory(),
        ];
    }
}
