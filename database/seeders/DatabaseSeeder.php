<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Altaf Ahmed',
            'email' => 'altaf124437@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $this->call(OrderSeeder::class);
    }
}
