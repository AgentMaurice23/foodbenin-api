<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(

            [
                'email' => 'admin@foodbenin.com'
            ],

            [
                'name' => 'Super Admin',

                'phone' => '0100000000',

                'password' => Hash::make('password')
            ]
        );

        $admin->assignRole('super_admin');
    }
}
