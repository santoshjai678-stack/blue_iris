<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'id'         => 1,
                'name'       => 'United States',
                'short_name' => 'US',
                'phone_code' => '+1',
                'status'     => 1,
            ],
            [
                'id'         => 2,
                'name'       => 'India',
                'short_name' => 'IN',
                'phone_code' => '+91',
                'status'     => 1,
            ],
        ];

        foreach ($countries as $data) {
            Country::updateOrCreate(
                ['id' => $data['id']],
                $data
            );
        }
    }
}