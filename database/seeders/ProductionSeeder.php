<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'electricity',
            'transport',
            'medical',
            'groceries',
            'phone and internet',
            'rent',
            'investment',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'color' => '#ffffff',
            ]);
        }
    }
}
