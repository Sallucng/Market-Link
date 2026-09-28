<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create settings table if not already present
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group')->nullable()->index();
                $table->string('type')->default('string');
                $table->string('label')->nullable();
                $table->text('description')->nullable();
                $table->text('options')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Add preferences to users if not present
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('preferences')->nullable();
            });
        }

        // 3. Add settings to farmers if not present
        if (Schema::hasTable('farmers') && !Schema::hasColumn('farmers', 'settings')) {
            Schema::table('farmers', function (Blueprint $table) {
                $table->json('settings')->nullable();
            });
        }

        // 4. Seed default platform configurations if settings table is empty
        if (DB::table('settings')->count() === 0) {
            $now = now();
            $defaults = [
                // Market & Pre-Order Rules
                [
                    'key' => 'default_cutoff_hours',
                    'value' => '2',
                    'group' => 'market_rules',
                    'type' => 'integer',
                    'label' => 'Default Pre-Order Cutoff (Hours)',
                    'description' => 'Standard hours before market opening when pre-orders lock automatically.',
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'max_preorder_days',
                    'value' => '7',
                    'group' => 'market_rules',
                    'type' => 'integer',
                    'label' => 'Advance Pre-Order Window (Days)',
                    'description' => 'Maximum number of days ahead that shoppers can place reserve orders.',
                    'sort_order' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'allow_same_day_orders',
                    'value' => '1',
                    'group' => 'market_rules',
                    'type' => 'boolean',
                    'label' => 'Allow Same-Day Pre-Orders',
                    'description' => 'Permit customers to place pre-orders on market morning before stall cutoff time.',
                    'sort_order' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'min_platform_order',
                    'value' => '0',
                    'group' => 'market_rules',
                    'type' => 'integer',
                    'label' => 'Minimum Order Value ($)',
                    'description' => 'Platform-wide minimum total required to reserve a stall order (0 = no minimum).',
                    'sort_order' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'max_active_listings_per_farmer',
                    'value' => '50',
                    'group' => 'market_rules',
                    'type' => 'integer',
                    'label' => 'Max Active Products Per Stall',
                    'description' => 'Listing cap to keep stall menus fresh and manageable.',
                    'sort_order' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                // Vendor & Stall Policies
                [
                    'key' => 'auto_approve_farmers',
                    'value' => '0',
                    'group' => 'vendor_policies',
                    'type' => 'boolean',
                    'label' => 'Auto-Approve New Growers',
                    'description' => 'If enabled, new farmer accounts are instantly verified without manual admin intervention.',
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'require_stall_coordinates',
                    'value' => '1',
                    'group' => 'vendor_policies',
                    'type' => 'boolean',
                    'label' => 'Require Stall GPS Coordinates',
                    'description' => 'Enforce exact map pin pinning before a stall appears on interactive market directories.',
                    'sort_order' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'allow_farmer_order_cancellation',
                    'value' => '1',
                    'group' => 'vendor_policies',
                    'type' => 'boolean',
                    'label' => 'Allow Growers to Cancel Orders',
                    'description' => 'Allow vendors to cancel and refund reservations in case of unforeseen harvest crop loss.',
                    'sort_order' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'require_stock_tracking',
                    'value' => '1',
                    'group' => 'vendor_policies',
                    'type' => 'boolean',
                    'label' => 'Enforce Real-Time Stock Tracking',
                    'description' => 'Immediately decrement stock when pre-orders are confirmed to prevent overselling.',
                    'sort_order' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                // Notifications & Alerts
                [
                    'key' => 'enable_in_app_notifications',
                    'value' => '1',
                    'group' => 'notifications',
                    'type' => 'boolean',
                    'label' => 'In-App Alerts System',
                    'description' => 'Push in-app alerts for order placement, status changes, and stall announcements.',
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'pickup_reminder_lead_hours',
                    'value' => '2',
                    'group' => 'notifications',
                    'type' => 'integer',
                    'label' => 'Pickup Reminder Window (Hours)',
                    'description' => 'Lead time before customer pickup time slot when automated reminders trigger.',
                    'sort_order' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'low_stock_threshold_default',
                    'value' => '5',
                    'group' => 'notifications',
                    'type' => 'integer',
                    'label' => 'Low Stock Warning Threshold',
                    'description' => 'Display restock alerts when product inventory drops below this quantity.',
                    'sort_order' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'admin_email_alerts',
                    'value' => '1',
                    'group' => 'notifications',
                    'type' => 'boolean',
                    'label' => 'Daily Admin Operations Digest',
                    'description' => 'Consolidate new market registrations and pending verifications into a daily digest.',
                    'sort_order' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                // Platform Display & Alerts
                [
                    'key' => 'site_title',
                    'value' => 'MarketLink — Local Farmers Network',
                    'group' => 'platform_display',
                    'type' => 'string',
                    'label' => 'Platform Brand Title',
                    'description' => 'Brand name displayed across customer storefront and browser headers.',
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'support_email',
                    'value' => 'support@marketlink.org',
                    'group' => 'platform_display',
                    'type' => 'string',
                    'label' => 'Helpdesk Contact Email',
                    'description' => 'Public contact address displayed on contact and checkout receipt pages.',
                    'sort_order' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'maintenance_mode',
                    'value' => '0',
                    'group' => 'platform_display',
                    'type' => 'boolean',
                    'label' => 'Platform Maintenance Banner',
                    'description' => 'Display a subtle advisory banner announcing upcoming scheduled maintenance.',
                    'sort_order' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'emergency_broadcast_active',
                    'value' => '0',
                    'group' => 'platform_display',
                    'type' => 'boolean',
                    'label' => 'Emergency Broadcast Banner',
                    'description' => 'Show high-priority broadcast banner across all public customer pages.',
                    'sort_order' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'key' => 'emergency_broadcast_message',
                    'value' => 'Weather Notice: Saturday outdoor stalls are moved inside Pavilion B due to light rain.',
                    'group' => 'platform_display',
                    'type' => 'text',
                    'label' => 'Broadcast Banner Text',
                    'description' => 'Message content for the emergency announcement banner.',
                    'sort_order' => 5,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ];

            DB::table('settings')->insert($defaults);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('preferences');
            });
        }

        if (Schema::hasTable('farmers') && Schema::hasColumn('farmers', 'settings')) {
            Schema::table('farmers', function (Blueprint $table) {
                $table->dropColumn('settings');
            });
        }
    }
};
