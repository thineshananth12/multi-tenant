<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'email_verified_at'=> '2025:05:02 00:00:00',
            'password' => bcrypt('1234'),
            'role'  => 1
        ]);
        
    }
}
