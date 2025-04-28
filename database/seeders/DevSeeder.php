<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Person;
use App\Models\RecurringExpense;
use App\Models\RecurringIncome;
use App\Models\RecurringTransfer;
use App\Models\Tag;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DevSeeder extends Seeder
{
    private Collection $categories;

    private Collection $people;

    private Collection $tags;

    public function run(): void
    {
        $this->categories = Category::factory()->count(10)->create();
        $this->people = Person::factory()->count(15)->create();
        $this->tags = Tag::factory()->count(10)->create();

        $this->performSeeding($this->user());
        $this->performSeeding($this->familyMember());
    }

    private function performSeeding(User $user): void
    {
        $accounts = Account::factory()->for($user)->count(5)->create();

        Income::factory()->for($user)->count(100)
            ->recycle($accounts)
            ->recycle($this->categories)
            ->recycle($this->people)
            ->recycle($this->tags)
            ->create();

        Expense::factory()->for($user)->count(100)
            ->recycle($accounts)
            ->recycle($this->categories)
            ->recycle($this->people)
            ->recycle($this->tags)
            ->create();

        Transfer::factory()->for($user)->count(20)
            ->recycle($accounts)
            ->recycle($this->tags)
            ->create();

        RecurringIncome::factory()->for($user)->count(3)->create();
        RecurringExpense::factory()->for($user)->count(3)->create();
        RecurringTransfer::factory()->for($user)->count(3)->create();
    }

    private function user(): User
    {
        return User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    private function familyMember(): User
    {
        return User::factory()->create([
            'name' => 'Family Member',
            'email' => 'family@example.com',
        ]);
    }
}
