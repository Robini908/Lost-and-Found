<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the columns directly using ALTER TABLE
        DB::statement('ALTER TABLE reward_histories DROP COLUMN type, DROP COLUMN category');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the columns back if needed
        Schema::table('reward_histories', function (Blueprint $table) {
            $table->string('type', 20)->after('user_id');
            $table->string('category', 20)->after('description');
        });
    }
};
