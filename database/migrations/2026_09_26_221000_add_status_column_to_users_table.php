<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status')->default('active')->after('role');
            });

            DB::table('users')->update([
                'status' => DB::raw("CASE WHEN is_active = 0 THEN 'suspended' WHEN is_approved = 0 THEN 'pending' ELSE 'active' END"),
            ]);
        }
    }

    public function down(): void
    {
        // non-destructive
    }
};
