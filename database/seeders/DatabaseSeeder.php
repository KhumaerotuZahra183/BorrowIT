<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'department' => 'IT',
                'role' => 'Admin',
                'status' => 'Active',
                'password' => bcrypt('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@ptbpi.co.id'],
            [
                'name' => 'Borrow User',
                'department' => 'Operations',
                'role' => 'User',
                'status' => 'Active',
                'password' => bcrypt('password'),
            ]
        );

        Asset::firstOrCreate(
            ['asset_number' => 'BPI-SO-0001'],
            [
                'asset_id' => 'BPI-26-0001',
                'asset_name' => 'Projector',
                'available' => 1,
            ]
        );

        Asset::firstOrCreate(
            ['asset_number' => 'BPI-SO-0002'],
            [
                'asset_id' => 'BPI-26-0002',
                'asset_name' => 'Remote',
                'available' => 1,
            ]
        );
    }
}
