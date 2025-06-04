<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles if they don't exist
        UserRole::firstOrCreate(
            ['role' => 'admin'],
            [
                'role' => 'admin',
                'permissions' => json_encode(['manage_users', 'view_reports', 'manage_incidents']),
                'description' => 'Administrator that manages officer acounts and reports' 
            ]
        );

        UserRole::firstOrCreate(
            ['role' => 'officer'],
            [
                'role' => 'officer',
                'permissions' => json_encode(['view_incidents', 'respond_to_incidents', 'create_reports']),
                'description' => 'Police officer with access to incident management'
            ]
        );

        UserRole::firstOrCreate(
            ['role' => 'resident'],
            [
                'role' => 'resident',
                'permissions' => json_encode(['report_incidents', 'view_own_reports']),
                'description' => 'Regular resident user who can report incidents'
            ]
        );
    }
}