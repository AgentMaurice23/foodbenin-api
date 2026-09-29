<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic',
            'price' => 5000,
            'commission_rate' => 5,
            'max_products' => 50,
            'max_employees' => 2,
            'featured_restaurant' => false
        ]);

        Plan::create([
            'name' => 'Pro',
            'price' => 15000,
            'commission_rate' => 2,
            'max_products' => 500,
            'max_employees' => 20,
            'featured_restaurant' => true
        ]);
    }
}

