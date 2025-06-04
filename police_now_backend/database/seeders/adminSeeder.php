<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;


class adminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $adminUser = User::firstOrCreate(
            ['email' => 'admin@policenow.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('Police@123'), // Change this in production
                'full_name' => 'System Administrator',
                'phone_number' => '1234567890',
                'role_id' => UserRole::where('role', 'admin')->first()->id ?? null,
                'registration_date' => now(),
                'is_verified' => true,
                'verification_status' => 'verified',
                'address' => 'Police Headquarters',
                'profile_image_url' => null
            ]
        );
    }
}