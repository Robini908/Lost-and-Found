<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeDateLostNullableInLostItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Make the date_lost column nullable
            $table->date('date_lost')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Revert the date_lost column to not nullable
            $table->date('date_lost')->nullable(false)->change();
        });
    }
}
