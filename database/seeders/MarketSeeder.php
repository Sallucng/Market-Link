<?php

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $markets = [
            [
                'name'           => 'Greenfield Central Farmers Market',
                'location'       => 'Greenfield',
                'address'        => '100 Market Square, Greenfield, CA 95001',
                'latitude'       => 37.7749000,
                'longitude'      => -122.4194000,
                'operating_days' => ['Saturday', 'Sunday'],
                'open_time'      => '08:00',
                'close_time'     => '14:00',
                'status'         => 'active',
                'image_url'      => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name'           => 'Ferry Plaza Farmers Market',
                'location'       => 'San Francisco',
                'address'        => '1 Ferry Building, San Francisco, CA 94111',
                'latitude'       => 37.7955000,
                'longitude'      => -122.3937000,
                'operating_days' => ['Tuesday', 'Thursday', 'Saturday'],
                'open_time'      => '08:00',
                'close_time'     => '14:00',
                'status'         => 'active',
                'image_url'      => 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name'           => 'Riverside Waterfront Farmers Market',
                'location'       => 'Riverside',
                'address'        => '250 River Walk Dr, Riverside, CA 95002',
                'latitude'       => 37.7833000,
                'longitude'      => -122.4167000,
                'operating_days' => ['Wednesday', 'Friday'],
                'open_time'      => '15:00',
                'close_time'     => '19:00',
                'status'         => 'active',
                'image_url'      => 'https://images.unsplash.com/photo-1516253593875-bd7ba052fbc5?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($markets as $m) {
            Market::firstOrCreate(
                ['name' => $m['name']],
                $m
            );
        }
    }
}
