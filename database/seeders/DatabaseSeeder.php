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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'department' => 'IT',
            'role' => 'Admin',
            'status' => 'Active',
        ]);

        Asset::create([
            'asset_id' => 'BPI-26-0001',
            'asset_number' => 'BPI-SO-0001',
            'asset_name' => 'Projector',
            'available' => 1,
        ]);

        Asset::create([
            'asset_id' => 'BPI-26-0002',
            'asset_number' => 'BPI-SO-0002',
            'asset_name' => 'Remote',
            'available' => 1,
        ]);
    }
}
