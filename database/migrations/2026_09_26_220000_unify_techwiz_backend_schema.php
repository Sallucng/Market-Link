<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to unify MarketLink web and Techwiz backend schemas.
     */
    public function up(): void
    {
        // 1. Categories - add is_active
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'is_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('description');
            });
            DB::table('categories')->update(['is_active' => true]);
        }

        // 2. Farmers - add Techwiz profile fields
        if (Schema::hasTable('farmers')) {
            Schema::table('farmers', function (Blueprint $table) {
                if (!Schema::hasColumn('farmers', 'business_name')) {
                    $table->string('business_name')->nullable()->after('market_id');
                }
                if (!Schema::hasColumn('farmers', 'farm_name')) {
                    $table->string('farm_name')->nullable()->after('business_name');
                }
                if (!Schema::hasColumn('farmers', 'stall_number')) {
                    $table->string('stall_number')->nullable()->after('contact_number');
                }
                if (!Schema::hasColumn('farmers', 'business_license')) {
                    $table->string('business_license')->nullable()->after('stall_number');
                }
                if (!Schema::hasColumn('farmers', 'city')) {
                    $table->string('city')->nullable()->after('address');
                }
                if (!Schema::hasColumn('farmers', 'state')) {
                    $table->string('state')->nullable()->after('city');
                }
                if (!Schema::hasColumn('farmers', 'postal_code')) {
                    $table->string('postal_code')->nullable()->after('state');
                }
                if (!Schema::hasColumn('farmers', 'pickup_start_time')) {
                    $table->string('pickup_start_time')->nullable()->after('pickup_time_windows');
                }
                if (!Schema::hasColumn('farmers', 'pickup_end_time')) {
                    $table->string('pickup_end_time')->nullable()->after('pickup_start_time');
                }
                if (!Schema::hasColumn('farmers', 'order_cutoff_time')) {
                    $table->string('order_cutoff_time')->nullable()->after('pickup_end_time');
                }
                if (!Schema::hasColumn('farmers', 'is_approved')) {
                    $table->boolean('is_approved')->default(true)->after('order_cutoff_time');
                }
                if (!Schema::hasColumn('farmers', 'approval_status')) {
                    $table->string('approval_status')->default('approved')->after('is_approved');
                }
                if (!Schema::hasColumn('farmers', 'description')) {
                    $table->text('description')->nullable()->after('bio');
                }
                if (!Schema::hasColumn('farmers', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('approval_status');
                }
            });

            // Backfill business_name and farm_name from stall_name
            DB::table('farmers')->whereNull('business_name')->update([
                'business_name' => DB::raw('stall_name'),
                'farm_name' => DB::raw('stall_name'),
                'is_approved' => true,
                'approval_status' => 'approved',
            ]);
        }

        // 3. Markets - add status and open/close times
        if (Schema::hasTable('markets')) {
            Schema::table('markets', function (Blueprint $table) {
                if (!Schema::hasColumn('markets', 'status')) {
                    $table->string('status')->default('active')->after('map_provider');
                }
                if (!Schema::hasColumn('markets', 'open_time')) {
                    $table->string('open_time')->default('08:00')->after('timings');
                }
                if (!Schema::hasColumn('markets', 'close_time')) {
                    $table->string('close_time')->default('14:00')->after('open_time');
                }
                if (!Schema::hasColumn('markets', 'location')) {
                    $table->string('location')->nullable()->after('address');
                }
            });
            DB::table('markets')->whereNull('location')->update([
                'location' => DB::raw('address'),
            ]);
        }

        // 4. Products - add slug, weekly_quota, is_moderated, status, farmer_profile_id
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'farmer_profile_id')) {
                    $table->unsignedBigInteger('farmer_profile_id')->nullable()->after('farmer_id');
                }
                if (!Schema::hasColumn('products', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (!Schema::hasColumn('products', 'weekly_quota')) {
                    $table->integer('weekly_quota')->default(0)->after('weekly_recurring_stock');
                }
                if (!Schema::hasColumn('products', 'is_moderated')) {
                    $table->boolean('is_moderated')->default(false)->after('is_available');
                }
                if (!Schema::hasColumn('products', 'status')) {
                    $table->string('status')->default('available')->after('is_sold_out');
                }
                if (!Schema::hasColumn('products', 'image')) {
                    $table->string('image')->nullable()->after('image_url');
                }
            });

            DB::table('products')->update([
                'farmer_profile_id' => DB::raw('farmer_id'),
                'status' => DB::raw("CASE WHEN is_sold_out = 1 THEN 'sold_out' ELSE 'available' END"),
                'image' => DB::raw('image_url'),
            ]);
        }

        // 5. Orders - add status, decline_reason, pickup_slot_id, pickup_time, farmer_profile_id
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'farmer_profile_id')) {
                    $table->unsignedBigInteger('farmer_profile_id')->nullable()->after('farmer_id');
                }
                if (!Schema::hasColumn('orders', 'status')) {
                    $table->string('status')->default('pending')->after('order_status');
                }
                if (!Schema::hasColumn('orders', 'decline_reason')) {
                    $table->text('decline_reason')->nullable()->after('status');
                }
                if (!Schema::hasColumn('orders', 'pickup_slot_id')) {
                    $table->unsignedBigInteger('pickup_slot_id')->nullable()->after('pickup_time_slot');
                }
                if (!Schema::hasColumn('orders', 'pickup_time')) {
                    $table->string('pickup_time')->nullable()->after('pickup_slot_id');
                }
                if (!Schema::hasColumn('orders', 'pickup_slot')) {
                    $table->string('pickup_slot')->nullable()->after('pickup_time');
                }
                if (!Schema::hasColumn('orders', 'cutoff_time')) {
                    $table->dateTime('cutoff_time')->nullable()->after('pickup_slot');
                }
            });

            DB::table('orders')->update([
                'farmer_profile_id' => DB::raw('farmer_id'),
                'status' => DB::raw("CASE WHEN order_status = 'placed' THEN 'pending' ELSE order_status END"),
                'pickup_time' => DB::raw('pickup_time_slot'),
                'pickup_slot' => DB::raw('pickup_time_slot'),
            ]);
        }

        // 6. Reviews - add farmer_reply, is_moderated, farmer_profile_id
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'farmer_profile_id')) {
                    $table->unsignedBigInteger('farmer_profile_id')->nullable()->after('farmer_id');
                }
                if (!Schema::hasColumn('reviews', 'farmer_reply')) {
                    $table->text('farmer_reply')->nullable()->after('farmer_response');
                }
                if (!Schema::hasColumn('reviews', 'is_moderated')) {
                    $table->boolean('is_moderated')->default(false)->after('responded_at');
                }
            });

            DB::table('reviews')->update([
                'farmer_profile_id' => DB::raw('farmer_id'),
                'farmer_reply' => DB::raw('farmer_response'),
            ]);
        }

        // 7. Favorites - add farmer_id, product_id, market_id, user_id, favoritable
        if (Schema::hasTable('favorites')) {
            Schema::table('favorites', function (Blueprint $table) {
                if (!Schema::hasColumn('favorites', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('customer_id');
                }
                if (!Schema::hasColumn('favorites', 'farmer_id')) {
                    $table->unsignedBigInteger('farmer_id')->nullable()->after('customer_id');
                }
                if (!Schema::hasColumn('favorites', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->after('farmer_id');
                }
                if (!Schema::hasColumn('favorites', 'market_id')) {
                    $table->unsignedBigInteger('market_id')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('favorites', 'favoritable_type')) {
                    $table->string('favoritable_type')->nullable()->after('item_type');
                }
                if (!Schema::hasColumn('favorites', 'favoritable_id')) {
                    $table->unsignedBigInteger('favoritable_id')->nullable()->after('favoritable_type');
                }
            });

            DB::table('favorites')->update([
                'user_id' => DB::raw('customer_id'),
            ]);
        }

        // 8. Announcements - add admin_id, target_role, expires_at
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'admin_id')) {
                    $table->unsignedBigInteger('admin_id')->nullable()->after('created_by');
                }
                if (!Schema::hasColumn('announcements', 'message')) {
                    $table->text('message')->nullable()->after('content');
                }
                if (!Schema::hasColumn('announcements', 'target_role')) {
                    $table->string('target_role')->default('all')->after('badge_type');
                }
                if (!Schema::hasColumn('announcements', 'target_audience')) {
                    $table->string('target_audience')->default('all')->after('target_role');
                }
                if (!Schema::hasColumn('announcements', 'expires_at')) {
                    $table->dateTime('expires_at')->nullable()->after('target_role');
                }
            });

            DB::table('announcements')->update([
                'admin_id' => DB::raw('created_by'),
                'message' => DB::raw('content'),
                'target_audience' => DB::raw("COALESCE(target_role, 'all')"),
            ]);
        }

        // 9. Carts table
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->timestamps();

                $table->unique(['customer_id', 'product_id']);
            });
        }

        // 10. Pickup slots table
        if (!Schema::hasTable('pickup_slots')) {
            Schema::create('pickup_slots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('farmer_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->string('day_of_week');
                $table->string('start_time');
                $table->string('end_time');
                $table->string('cutoff_time')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 11. Reports table
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
                $table->string('report_type');
                $table->date('report_date');
                $table->integer('total_orders')->default(0);
                $table->decimal('total_revenue', 10, 2)->default(0);
                $table->integer('active_farmers')->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 12. Farmer-Market pivot table
        if (!Schema::hasTable('farmer_market')) {
            Schema::create('farmer_market', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('farmer_profile_id')->nullable();
                $table->unsignedBigInteger('farmer_id')->nullable();
                $table->foreignId('market_id')->constrained('markets')->cascadeOnDelete();
                $table->string('assigned_stall')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });

            // Populate farmer_market from farmers.market_id
            $farmers = DB::table('farmers')->whereNotNull('market_id')->get();
            foreach ($farmers as $f) {
                DB::table('farmer_market')->insert([
                    'farmer_profile_id' => $f->id,
                    'farmer_id' => $f->id,
                    'market_id' => $f->market_id,
                    'assigned_stall' => $f->stall_name ?? 'Stall #1',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep non-destructive
    }
};
