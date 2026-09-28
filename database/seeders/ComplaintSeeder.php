<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\Farmer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        $admin = User::where('role', 'admin')->first();
        $farmer = Farmer::first();
        $secondFarmer = Farmer::skip(1)->first();

        if (!$customer || !$farmer) {
            return;
        }

        $order = Order::where('customer_id', $customer->id)->where('farmer_id', $farmer->id)->first();

        // 1. Pending complaint
        Complaint::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'farmer_id' => $farmer->id,
                'subject' => 'Bruised and overripe tomatoes in weekend pickup box',
            ],
            [
                'order_id' => $order?->id,
                'complaint_type' => 'poor_quality',
                'description' => "When I picked up my pre-ordered heirloom tomatoes on Saturday, more than half of the batch was severely bruised and leaking juice inside the box. I brought it up to the stall assistant, but they were in a rush and told me to submit a claim online. I would appreciate replacement or credit for the next harvest.",
                'status' => 'pending',
                'admin_notes' => null,
                'resolved_by' => null,
                'resolved_at' => null,
            ]
        );

        // 2. Under Review complaint
        if ($secondFarmer) {
            Complaint::updateOrCreate(
                [
                    'customer_id' => $customer->id,
                    'farmer_id' => $secondFarmer->id,
                    'subject' => 'Stall closed 30 minutes earlier than scheduled pickup cutoff',
                ],
                [
                    'order_id' => null,
                    'complaint_type' => 'unfulfilled_order',
                    'description' => "Arrived at 11:30 AM within the stated 11:00 AM - 12:30 PM pickup window, but the stall was already completely packed up and vacated. Unable to collect reserved greens.",
                    'status' => 'under_review',
                    'admin_notes' => "Contacted market manager to verify stall attendance logs for Saturday morning.",
                    'resolved_by' => null,
                    'resolved_at' => null,
                ]
            );
        }

        // 3. Resolved complaint
        Complaint::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'farmer_id' => $farmer->id,
                'subject' => 'Price charged in-person differed from MarketLink listed price',
            ],
            [
                'order_id' => null,
                'complaint_type' => 'pricing_issue',
                'description' => "Stall operator quoted $5.00/lb for honeycrisp apples when the online platform catalog listed $4.25/lb. Settled in person but wanted to report the discrepancy.",
                'status' => 'resolved',
                'admin_notes' => "Grower was notified regarding price sync requirements. Grower apologized and offered a $5 credit voucher at the next market day.",
                'resolved_by' => $admin?->id,
                'resolved_at' => now()->subDays(2),
            ]
        );
    }
}
