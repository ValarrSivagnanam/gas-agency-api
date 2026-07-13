<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use updateOrCreate to avoid duplicate entries if you run the seeder twice
        User::updateOrCreate(
            ['email' => 'asmvalarr@gmail.com'], // Match condition
            [
                'name' => 'Sreekanth',
                'phone' => '9600304546',
                'role' => 'admin',
                'password' => Hash::make('valarr123'), // Secure way to hash passwords
            ]
        );
    }
}
