<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'electricity',
            'transport',
            'medical',
            'groceries',
            'phone and internet',
            'rent',
            'investment',
            'food',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
    }
}
