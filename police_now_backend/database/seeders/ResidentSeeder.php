<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Resident;
use Illuminate\Support\Facades\Hash;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the resident role
        $residentRole = UserRole::where('role', 'resident')->first();

        if (!$residentRole) {
            $this->command->error('Resident role not found. Please run UserRoleSeeder first.');
            return;
        }

        // Create test residents
        $residents = [
            [
                'username' => 'Jude',
                'email' => 'delrosariojude61@gmail.com',
                'password' => 'Password123!',
                'full_name' => 'Jude Delrosario',
                'phone_number' => '+1234567890',
                'address' => '123 Main St, City',
                'emergency_contact_name' => 'Jane Doe',
                'emergency_contact_number' => '+1234567891',
            ],
            [
                'username' => 'jane.smith',
                'email' => 'jane.smith@example.com',
                'password' => 'Password123!',
                'full_name' => 'Jane Smith',
                'phone_number' => '+1234567892',
                'address' => '456 Oak St, City',
                'emergency_contact_name' => 'John Smith',
                'emergency_contact_number' => '+1234567893',
            ],
            [
                'username' => 'test.resident',
                'email' => 'test.resident@example.com',
                'password' => 'Password123!',
                'full_name' => 'Test Resident',
                'phone_number' => '+1234567894',
                'address' => '789 Pine St, City',
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_number' => '+1234567895',
            ],
        ];

        foreach ($residents as $residentData) {
            // Create the user
            $user = User::firstOrCreate(
                ['email' => $residentData['email']],
                [
                    'username' => $residentData['username'],
                    'email' => $residentData['email'],
                    'password' => Hash::make($residentData['password']),
                    'full_name' => $residentData['full_name'],
                    'phone_number' => $residentData['phone_number'],
                    'role_id' => $residentRole->id,
                    'registration_date' => now(),
                    'is_verified' => true,
                    'verification_status' => 'verified',
                    'address' => $residentData['address'],
                ]
            );

            // Create the resident profile if it doesn't exist
            if (!$user->resident) {
                Resident::create([
                    'user_id' => $user->id,
                    'emergency_contact_name' => $residentData['emergency_contact_name'],
                    'emergency_contact_number' => $residentData['emergency_contact_number'],
                ]);
            }
        }

        $this->command->info('Residents seeded successfully!');
    }
} 