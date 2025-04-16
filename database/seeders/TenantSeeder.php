<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant1 = \App\Models\Tenant::create([
            'name' => 'Tenant 1',
            'prefix' => 'tenant1t',
        ]);
        
        $tenant2 = \App\Models\Tenant::create([
            'name' => 'Tenant 2',
            'prefix' => 'tenant2',
        ]);

        $tenant1Users = \App\Models\User::factory(48)->create([
            'tenant_id' => $tenant1->id,
        ]);

        $tenant2Users = \App\Models\User::factory(48)->create([
            'tenant_id' => $tenant2->id,
            
        ]);

        $this->call(UserSeeder::class);
    }
}
