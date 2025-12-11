<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone_number' => '081234567890',
            'address' => 'Jakarta, Indonesia',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Owner User
        User::create([
            'username' => 'owner',
            'email' => 'owner@example.com',
            'phone_number' => '081234567891',
            'address' => 'Bandung, Indonesia',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Create Customer User
        User::create([
            'username' => 'customer',
            'email' => 'customer@example.com',
            'phone_number' => '081234567892',
            'address' => 'Surabaya, Indonesia',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        // $this->call([
        //     OrderSeeder::class,
        // ]);
    }
}
