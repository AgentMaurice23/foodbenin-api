<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $permissions = [

            'manage_restaurants',

            'manage_products',

            'manage_orders',

            'manage_payments',

            'manage_drivers',

            'manage_reviews',

            'manage_subscriptions',

            'manage_users',

            'manage_platform'
        ];

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }
    }

}
