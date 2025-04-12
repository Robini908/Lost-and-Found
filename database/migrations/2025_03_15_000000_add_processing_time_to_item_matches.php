<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_matches', function (Blueprint $table) {
            $table->float('processing_time_ms')->nullable()->after('matched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_matches', function (Blueprint $table) {
            $table->dropColumn('processing_time_ms');
        });
    }
};
