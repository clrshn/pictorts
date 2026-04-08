<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create offices first
        $offices = [
            ['code' => 'PICTO', 'name' => 'PICTO Office'],
            ['code' => 'HR', 'name' => 'Human Resources'],
            ['code' => 'FIN', 'name' => 'Finance Department'],
            ['code' => 'IT', 'name' => 'Information Technology'],
            ['code' => 'ADMIN', 'name' => 'Administration'],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate($office);
        }

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@pictorts.com'],
            [
                'name' => 'PICTO Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'office_id' => 1, // PICTO Office
            ]
        );

        // Create regular user
        $regularUser = User::firstOrCreate(
            ['email' => 'user@pictorts.com'],
            [
                'name' => 'PICTO User',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'office_id' => 1, // PICTO Office
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin login: admin@pictorts.com / admin123');
        $this->command->info('User login: user@pictorts.com / user123');
    }
}
