<?php

namespace Database\Seeders;

use App\Models\FinancialYear;
use Illuminate\Database\Seeder;

class FinancialYearSeeder extends Seeder
{
    public function run(): void
    {
        FinancialYear::factory()->createMany([
            ['start_date' => today()->subYears(3)],
            ['start_date' => today()->subYears(2)],
            ['start_date' => today()->subYear()],
            ['start_date' => today()],
        ]);
    }
}
