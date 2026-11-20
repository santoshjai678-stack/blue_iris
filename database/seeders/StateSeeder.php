<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Country;
use App\Models\Tenant\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usa = Country::where('short_name', 'US')->orWhere('name', 'United States')->first();
        $india = Country::where('short_name', 'IN')->orWhere('name', 'India')->first();

        // 1. United States (50 States)
        if ($usa) {
            $usStates = [
                ['name' => 'Kansas', 'code' => 'KS'],
                ['name' => 'Alabama', 'code' => 'AL'],
                ['name' => 'Alaska', 'code' => 'AK'],
                ['name' => 'Arizona', 'code' => 'AZ'],
                ['name' => 'Arkansas', 'code' => 'AR'],
                ['name' => 'California', 'code' => 'CA'],
                ['name' => 'Colorado', 'code' => 'CO'],
                ['name' => 'Connecticut', 'code' => 'CT'],
                ['name' => 'Delaware', 'code' => 'DE'],
                ['name' => 'Florida', 'code' => 'FL'],
                ['name' => 'Georgia', 'code' => 'GA'],
                ['name' => 'Hawaii', 'code' => 'HI'],
                ['name' => 'Idaho', 'code' => 'ID'],
                ['name' => 'Illinois', 'code' => 'IL'],
                ['name' => 'Indiana', 'code' => 'IN'],
                ['name' => 'Iowa', 'code' => 'IA'],
                ['name' => 'Kentucky', 'code' => 'KY'],
                ['name' => 'Louisiana', 'code' => 'LA'],
                ['name' => 'Maine', 'code' => 'ME'],
                ['name' => 'Maryland', 'code' => 'MD'],
                ['name' => 'Massachusetts', 'code' => 'MA'],
                ['name' => 'Michigan', 'code' => 'MI'],
                ['name' => 'Minnesota', 'code' => 'MN'],
                ['name' => 'Mississippi', 'code' => 'MS'],
                ['name' => 'Missouri', 'code' => 'MO'],
                ['name' => 'Montana', 'code' => 'MT'],
                ['name' => 'Nebraska', 'code' => 'NE'],
                ['name' => 'Nevada', 'code' => 'NV'],
                ['name' => 'New Hampshire', 'code' => 'NH'],
                ['name' => 'New Jersey', 'code' => 'NJ'],
                ['name' => 'New Mexico', 'code' => 'NM'],
                ['name' => 'New York', 'code' => 'NY'],
                ['name' => 'North Carolina', 'code' => 'NC'],
                ['name' => 'North Dakota', 'code' => 'ND'],
                ['name' => 'Ohio', 'code' => 'OH'],
                ['name' => 'Oklahoma', 'code' => 'OK'],
                ['name' => 'Oregon', 'code' => 'OR'],
                ['name' => 'Pennsylvania', 'code' => 'PA'],
                ['name' => 'Rhode Island', 'code' => 'RI'],
                ['name' => 'South Carolina', 'code' => 'SC'],
                ['name' => 'South Dakota', 'code' => 'SD'],
                ['name' => 'Tennessee', 'code' => 'TN'],
                ['name' => 'Texas', 'code' => 'TX'],
                ['name' => 'Utah', 'code' => 'UT'],
                ['name' => 'Vermont', 'code' => 'VT'],
                ['name' => 'Virginia', 'code' => 'VA'],
                ['name' => 'Washington', 'code' => 'WA'],
                ['name' => 'West Virginia', 'code' => 'WV'],
                ['name' => 'Wisconsin', 'code' => 'WI'],
                ['name' => 'Wyoming', 'code' => 'WY'],
            ];

            foreach ($usStates as $st) {
                State::updateOrCreate(
                    [
                        'country_id' => $usa->id,
                        'name'       => $st['name'],
                    ],
                    [
                        'country_id' => $usa->id,
                        'name'       => $st['name'],
                        'code'       => $st['code'],
                        'status'     => 1,
                    ]
                );
            }
        }

        // 2. India (28 States + 8 UTs)
        if ($india) {
            $indiaStates = [
                ['name' => 'Andhra Pradesh', 'code' => 'AP'],
                ['name' => 'Arunachal Pradesh', 'code' => 'AR'],
                ['name' => 'Assam', 'code' => 'AS'],
                ['name' => 'Bihar', 'code' => 'BR'],
                ['name' => 'Chhattisgarh', 'code' => 'CG'],
                ['name' => 'Goa', 'code' => 'GA'],
                ['name' => 'Gujarat', 'code' => 'GJ'],
                ['name' => 'Haryana', 'code' => 'HR'],
                ['name' => 'Himachal Pradesh', 'code' => 'HP'],
                ['name' => 'Jharkhand', 'code' => 'JH'],
                ['name' => 'Karnataka', 'code' => 'KA'],
                ['name' => 'Kerala', 'code' => 'KL'],
                ['name' => 'Madhya Pradesh', 'code' => 'MP'],
                ['name' => 'Maharashtra', 'code' => 'MH'],
                ['name' => 'Manipur', 'code' => 'MN'],
                ['name' => 'Meghalaya', 'code' => 'ML'],
                ['name' => 'Mizoram', 'code' => 'MZ'],
                ['name' => 'Nagaland', 'code' => 'NL'],
                ['name' => 'Odisha', 'code' => 'OD'],
                ['name' => 'Punjab', 'code' => 'PB'],
                ['name' => 'Rajasthan', 'code' => 'RJ'],
                ['name' => 'Sikkim', 'code' => 'SK'],
                ['name' => 'Tamil Nadu', 'code' => 'TN'],
                ['name' => 'Telangana', 'code' => 'TS'],
                ['name' => 'Tripura', 'code' => 'TR'],
                ['name' => 'Uttar Pradesh', 'code' => 'UP'],
                ['name' => 'Uttarakhand', 'code' => 'UK'],
                ['name' => 'West Bengal', 'code' => 'WB'],

                // Union Territories
                ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN'],
                ['name' => 'Chandigarh', 'code' => 'CH'],
                ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DH'],
                ['name' => 'Delhi', 'code' => 'DL'],
                ['name' => 'Jammu and Kashmir', 'code' => 'JK'],
                ['name' => 'Ladakh', 'code' => 'LA'],
                ['name' => 'Lakshadweep', 'code' => 'LD'],
                ['name' => 'Puducherry', 'code' => 'PY'],
            ];

            foreach ($indiaStates as $st) {
                State::updateOrCreate(
                    [
                        'country_id' => $india->id,
                        'name'       => $st['name'],
                    ],
                    [
                        'country_id' => $india->id,
                        'name'       => $st['name'],
                        'code'       => $st['code'],
                        'status'     => 1,
                    ]
                );
            }
        }
    }
}