<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        $user = $this->user();
        $accounts = Account::factory()->for($user)->count(5)->create();
        $categories = Category::factory()->count(10)->create();
        $people = Person::factory()->count(15)->create();

        Income::factory()->for($user)->count(100)
            ->recycle($accounts)
            ->recycle($categories)
            ->recycle($people)
            ->create();

        Expense::factory()->for($user)->count(100)
            ->recycle($accounts)
            ->recycle($categories)
            ->recycle($people)
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
