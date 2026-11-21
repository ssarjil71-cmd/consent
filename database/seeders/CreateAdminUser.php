<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Seeder
{
    public function run(): void
    {
        // Delete existing admin if exists
        Admin::where('email', 'admin@consent.local')->delete();
        
        // Create new admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@consent.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        $this->command->info('Admin user created successfully!');
    }
}
