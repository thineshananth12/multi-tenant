<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(48)->create();

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'user@mail.com',
            'email_verified_at'=> '2025:05:02 00:00:00',
            'password' => bcrypt('1234'),
            'role'  => 2
        ]);

        $this->call(TenantSeeder::class);
        $this->call(UserSeeder::class);
    }
}
