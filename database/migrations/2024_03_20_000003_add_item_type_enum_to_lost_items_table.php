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
        // First, update existing records
        DB::table('lost_items')
            ->where('item_type', 'lost')
            ->update(['item_type' => 'reported']);

        // Then modify the column
        Schema::table('lost_items', function (Blueprint $table) {
            // Drop the existing column if it exists
            if (Schema::hasColumn('lost_items', 'item_type')) {
                $table->dropColumn('item_type');
            }

            // Add the new column with enum constraint
            $table->enum('item_type', ['reported', 'searched', 'found'])
                  ->default('reported')
                  ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Convert back to a regular string column
            $table->string('item_type')->nullable()->change();
        });
    }
};
