<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->user();
    }

    private function user()
    {
        // Superadmin Account
        User::updateOrCreate([
            'email' => 'adita@gmail.com',
        ], [
            'name' => 'Superadmin RECT',
            'email' => 'adita@gmail.com',
            'role' => 'superadmin',
            'password' => 'adita',
        ]);
    }
}
