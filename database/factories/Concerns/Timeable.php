<?php

namespace Database\Factories\Concerns;

use Illuminate\Support\Carbon;

trait Timeable
{
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

    public function dayBeforeYesterday(): self
    {
        return $this->state(fn (array $attributes) => [
            'transacted_at' => Carbon::now()->subDays(2),
        ]);
    }
}
