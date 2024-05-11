<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),

            'user_id' => User::factory(),
        ];
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => Carbon::now(),
        ]);
    }
}
