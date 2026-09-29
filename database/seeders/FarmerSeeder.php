<?php

namespace Database\Seeders;

use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $markets = Market::all();

        // 1. Five Verified/Approved Farmers
        $approvedFarmersData = [
            [
                'name'              => 'John Peterson',
                'email'             => 'farmer.john@marketlink.com',
                'business_name'     => 'Sunrise Organic Acres',
                'description'       => 'Family-owned certified organic farm harvesting seasonal vegetables and heirloom tomatoes with sustainable practices.',
                'stall_number'      => 'Stall #14',
                'address'           => '450 Valley Road, Greenfield, CA 95001',
                'latitude'          => 37.7600000,
                'longitude'         => -122.4200000,
                'operating_days'    => ['Wednesday', 'Saturday', 'Sunday'],
                'pickup_start_time' => '08:00',
                'pickup_end_time'   => '13:00',
            ],
            [
                'name'              => 'Maria Gonzalez',
                'email'             => 'maria.orchards@marketlink.com',
                'business_name'     => 'Golden Valley Orchards',
                'description'       => 'Third-generation fruit orchard producing sun-ripened stone fruit, crisp apples, and fresh berry baskets.',
                'stall_number'      => 'Stall #22',
                'address'           => '820 Orchard Way, Salinas, CA 93901',
                'latitude'          => 37.7550000,
                'longitude'         => -122.4100000,
                'operating_days'    => ['Thursday', 'Saturday'],
                'pickup_start_time' => '08:30',
                'pickup_end_time'   => '13:30',
            ],
            [
                'name'              => 'David Miller',
                'email'             => 'miller.dairy@marketlink.com',
                'business_name'     => 'Pasture Ridge Dairy & Poultry',
                'description'       => 'Regenerative pasture-based family farm offering raw milk, artisan cheese, free-range chicken, and fresh brown eggs.',
                'stall_number'      => 'Stall #07',
                'address'           => '1200 Meadow Creek Rd, Modesto, CA 95350',
                'latitude'          => 37.7800000,
                'longitude'         => -122.4050000,
                'operating_days'    => ['Saturday', 'Sunday'],
                'pickup_start_time' => '08:00',
                'pickup_end_time'   => '14:00',
            ],
            [
                'name'              => 'Sarah Jenkins',
                'email'             => 'sarah.herbs@marketlink.com',
                'business_name'     => 'Emerald Blossom Herbals',
                'description'       => 'Pesticide-free specialty herb garden cultivating culinary herbs, microgreens, and medicinal botanicals.',
                'stall_number'      => 'Stall #31',
                'address'           => '310 Garden Lane, Napa, CA 94558',
                'latitude'          => 37.7900000,
                'longitude'         => -122.4150000,
                'operating_days'    => ['Tuesday', 'Thursday', 'Saturday'],
                'pickup_start_time' => '09:00',
                'pickup_end_time'   => '13:00',
            ],
            [
                'name'              => 'Robert Chen',
                'email'             => 'chen.roots@marketlink.com',
                'business_name'     => 'Pacific Harvest Farms',
                'description'       => 'Coastal vegetable growers specializing in organic root crops, brassicas, and crisp leafy greens.',
                'stall_number'      => 'Stall #05',
                'address'           => '640 Coastal Highway, Half Moon Bay, CA 94019',
                'latitude'          => 37.7700000,
                'longitude'         => -122.4250000,
                'operating_days'    => ['Friday', 'Saturday'],
                'pickup_start_time' => '08:00',
                'pickup_end_time'   => '12:30',
            ],
        ];

        foreach ($approvedFarmersData as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'username'          => explode('@', $data['email'])[0],
                    'password'          => Hash::make('Farmer123!'),
                    'role'              => 'farmer',
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $profile = FarmerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name'     => $data['business_name'],
                    'description'       => $data['description'],
                    'stall_number'      => $data['stall_number'],
                    'address'           => $data['address'],
                    'latitude'          => $data['latitude'],
                    'longitude'         => $data['longitude'],
                    'operating_days'    => $data['operating_days'],
                    'pickup_start_time' => $data['pickup_start_time'],
                    'pickup_end_time'   => $data['pickup_end_time'],
                    'is_approved'       => true,
                    'rejection_reason'  => null,
                ]
            );

            // Associate with 1 or 2 physical markets
            if ($markets->isNotEmpty()) {
                $assignedMarket = $markets[$index % $markets->count()];
                if (!$profile->markets()->where('market_id', $assignedMarket->id)->exists()) {
                    $profile->markets()->attach($assignedMarket->id, [
                        'assigned_stall' => $data['stall_number'],
                        'status'         => 'approved',
                    ]);
                }

                // Associate second market for top farmers
                if ($index < 2 && $markets->count() > 1) {
                    $secondMarket = $markets[($index + 1) % $markets->count()];
                    if (!$profile->markets()->where('market_id', $secondMarket->id)->exists()) {
                        $profile->markets()->attach($secondMarket->id, [
                            'assigned_stall' => $data['stall_number'],
                            'status'         => 'approved',
                        ]);
                    }
                }
            }
        }

        // 2. Two Pending Farmers
        $pendingFarmersData = [
            [
                'name'              => 'Thomas Wilson',
                'email'             => 'thomas.pending@marketlink.com',
                'business_name'     => 'Wilson Homestead Apiaries',
                'description'       => 'Family beekeepers seeking market placement for raw wildflower and clover honey.',
                'stall_number'      => 'Stall #45',
                'address'           => '950 Pine Ridge Rd, Fresno, CA 93720',
                'latitude'          => 37.7400000,
                'longitude'         => -122.4300000,
                'operating_days'    => ['Saturday'],
                'pickup_start_time' => '09:00',
                'pickup_end_time'   => '13:00',
            ],
            [
                'name'              => 'Elena Rostova',
                'email'             => 'elena.pending@marketlink.com',
                'business_name'     => 'Heritage Valley Mushroom Co.',
                'description'       => 'Indoor sustainable cultivation of gourmet culinary mushrooms including oyster and shiitake.',
                'stall_number'      => 'Stall #50',
                'address'           => '110 Industrial Way, Santa Cruz, CA 95060',
                'latitude'          => 37.7350000,
                'longitude'         => -122.4220000,
                'operating_days'    => ['Saturday', 'Sunday'],
                'pickup_start_time' => '08:30',
                'pickup_end_time'   => '12:30',
            ],
        ];

        foreach ($pendingFarmersData as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('Farmer123!'),
                    'role'              => 'farmer',
                    'status'            => 'pending',
                    'email_verified_at' => now(),
                ]
            );

            $profile = FarmerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name'     => $data['business_name'],
                    'description'       => $data['description'],
                    'stall_number'      => $data['stall_number'],
                    'address'           => $data['address'],
                    'latitude'          => $data['latitude'],
                    'longitude'         => $data['longitude'],
                    'operating_days'    => $data['operating_days'],
                    'pickup_start_time' => $data['pickup_start_time'],
                    'pickup_end_time'   => $data['pickup_end_time'],
                    'is_approved'       => false,
                    'rejection_reason'  => null,
                ]
            );

            if ($markets->isNotEmpty()) {
                $assignedMarket = $markets[0];
                if (!$profile->markets()->where('market_id', $assignedMarket->id)->exists()) {
                    $profile->markets()->attach($assignedMarket->id, [
                        'assigned_stall' => $data['stall_number'],
                        'status'         => 'pending',
                    ]);
                }
            }
        }
    }
}
