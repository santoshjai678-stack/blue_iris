<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Central\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::updateOrCreate(
            ['email' => 'superadmin@demo.com'],
            [
                'name'       => 'Super Administrator',
                'phone_no'   => '1234567890',
                'password'   => Hash::make('Test@123'),
                'status'     => 1,
            ]
        );

        SuperAdmin::updateOrCreate(
            ['email' => 'superadmin@blueiris.app'],
            [
                'name'       => 'Super Administrator',
                'phone_no'   => '1234567890',
                'password'   => Hash::make('Password@123'),
                'status'     => 1,
            ]
        );
    }
}