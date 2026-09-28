<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title'       => 'Welcome to MarketLink Platform',
                'message'     => 'Welcome to MarketLink! Connect directly with verified local farmers, browse fresh harvests, and pre-order quality produce for convenient weekend market pickup.',
                'target_role' => 'all',
                'is_active'   => true,
            ],
            [
                'title'       => 'Vendor Stall Guidelines & Health Inspection Update',
                'message'     => 'All participating farmers must submit their updated seasonal health inspection permits and market stall display plans before next month’s market opening.',
                'target_role' => 'farmer',
                'is_active'   => true,
            ],
            [
                'title'       => 'Weekly Inventory Cutoff Reminder',
                'message'     => 'Farmers are reminded to finalize and update their weekly stock quotas every Thursday by 8:00 PM to prepare for weekend customer pre-orders.',
                'target_role' => 'farmer',
                'is_active'   => true,
            ],
            [
                'title'       => 'Weekend Farmers Market Hours & Fresh Harvest Specials',
                'message'     => 'Check out our newly registered organic family farms this weekend! Enjoy early bird pickup windows and seasonal stone fruit specials.',
                'target_role' => 'customer',
                'is_active'   => true,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::firstOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
