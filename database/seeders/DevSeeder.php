<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        $user = $this->user();
        $accounts = Account::factory()->for($user)->count(5)->create();

        Income::factory()->for($user)->count(100)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['account_id' => $accounts->random()],
            ))
            ->create();

        Expense::factory()->for($user)->count(100)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['account_id' => $accounts->random()],
            ))
            ->create();
    }

    private function user(): User
    {
        return User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
