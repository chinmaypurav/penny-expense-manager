<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            'Salary',
            'Dividend',
            'Gift',

            'Electricity Bill',
            'Groceries',
            'Transportation',
            'Internet/Mobile Bills',
            'Apparels',
            'Leisure Activities',

            'Other',
        ])->each(function (string $category) {
            Category::create(['name' => $category]);
        });
    }
}
