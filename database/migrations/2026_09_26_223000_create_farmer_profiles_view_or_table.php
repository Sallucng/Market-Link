<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If not already existing, create view farmer_profiles pointing to farmers
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('CREATE VIEW IF NOT EXISTS farmer_profiles AS SELECT * FROM farmers');

            DB::statement('
                CREATE TRIGGER IF NOT EXISTS farmer_profiles_insert INSTEAD OF INSERT ON farmer_profiles
                BEGIN
                    INSERT INTO farmers (
                        user_id, market_id, stall_name, business_name, farm_name, contact_person, contact_number,
                        stall_number, business_license, address, city, state, postal_code, latitude, longitude,
                        operating_days, pickup_time_windows, pickup_start_time, pickup_end_time, order_cutoff_time,
                        cutoff_hours, bio, description, image_url, is_approved, approval_status, rejection_reason, settings, created_at, updated_at
                    ) VALUES (
                        NEW.user_id, NEW.market_id, COALESCE(NEW.stall_name, NEW.business_name, NEW.farm_name, \'Farm Stall\'),
                        COALESCE(NEW.business_name, NEW.farm_name, NEW.stall_name),
                        COALESCE(NEW.farm_name, NEW.business_name, NEW.stall_name),
                        COALESCE(NEW.contact_person, \'Farm Operator\'),
                        COALESCE(NEW.contact_number, \'555-0100\'),
                        NEW.stall_number, NEW.business_license, NEW.address, NEW.city, NEW.state, NEW.postal_code,
                        NEW.latitude, NEW.longitude, NEW.operating_days, NEW.pickup_time_windows, NEW.pickup_start_time,
                        NEW.pickup_end_time, NEW.order_cutoff_time, COALESCE(NEW.cutoff_hours, 2),
                        COALESCE(NEW.bio, NEW.description), COALESCE(NEW.description, NEW.bio),
                        NEW.image_url, COALESCE(NEW.is_approved, 1), COALESCE(NEW.approval_status, \'approved\'),
                        NEW.rejection_reason, NEW.settings, COALESCE(NEW.created_at, datetime(\'now\')), COALESCE(NEW.updated_at, datetime(\'now\'))
                    );
                END;
            ');

            DB::statement('
                CREATE TRIGGER IF NOT EXISTS farmer_profiles_update INSTEAD OF UPDATE ON farmer_profiles
                BEGIN
                    UPDATE farmers SET
                        market_id = NEW.market_id,
                        stall_name = COALESCE(NEW.stall_name, farmers.stall_name),
                        business_name = COALESCE(NEW.business_name, farmers.business_name),
                        farm_name = COALESCE(NEW.farm_name, farmers.farm_name),
                        contact_person = COALESCE(NEW.contact_person, farmers.contact_person),
                        contact_number = COALESCE(NEW.contact_number, farmers.contact_number),
                        stall_number = NEW.stall_number,
                        business_license = NEW.business_license,
                        address = NEW.address,
                        city = NEW.city,
                        state = NEW.state,
                        postal_code = NEW.postal_code,
                        latitude = NEW.latitude,
                        longitude = NEW.longitude,
                        operating_days = NEW.operating_days,
                        pickup_time_windows = NEW.pickup_time_windows,
                        pickup_start_time = NEW.pickup_start_time,
                        pickup_end_time = NEW.pickup_end_time,
                        order_cutoff_time = NEW.order_cutoff_time,
                        cutoff_hours = NEW.cutoff_hours,
                        bio = COALESCE(NEW.bio, NEW.description, farmers.bio),
                        description = COALESCE(NEW.description, NEW.bio, farmers.description),
                        image_url = NEW.image_url,
                        is_approved = NEW.is_approved,
                        approval_status = NEW.approval_status,
                        rejection_reason = NEW.rejection_reason,
                        settings = NEW.settings,
                        updated_at = COALESCE(NEW.updated_at, datetime(\'now\'))
                    WHERE id = OLD.id;
                END;
            ');
        } elseif ($driver === 'mysql') {
            DB::statement('CREATE OR REPLACE VIEW farmer_profiles AS SELECT * FROM farmers');
        }
    }

    public function down(): void
    {
        // non-destructive
    }
};
