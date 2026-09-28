<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('farmers') && !Schema::hasColumn('farmers', 'description')) {
            Schema::table('farmers', function (Blueprint $table) {
                $table->text('description')->nullable()->after('bio');
            });

            DB::table('farmers')->update([
                'description' => DB::raw('bio'),
            ]);
        }
    }

    public function down(): void
    {
        // non-destructive
    }
};
