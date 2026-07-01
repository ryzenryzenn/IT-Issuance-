<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@meatplus.ph'],
            [
                'name'              => 'System Administrator',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ],
        );
        $admin->syncRoles(['Admin']);

        $itStaff = User::firstOrCreate(
            ['email' => 'itstaff@meatplus.ph'],
            [
                'name'              => 'IT Staff',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ],
        );
        $itStaff->syncRoles(['IT Staff']);

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@meatplus.ph'],
            [
                'name'              => 'Viewer',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ],
        );
        $viewer->syncRoles(['Viewer']);
    }
}
