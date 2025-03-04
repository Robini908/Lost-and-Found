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
        Schema::table('lost_items', function (Blueprint $table) {
            if (!Schema::hasColumn('lost_items', 'location_type')) {
                $table->string('location_type')->nullable();
            }
            if (!Schema::hasColumn('lost_items', 'location_address')) {
                $table->string('location_address')->nullable();
            }
            if (!Schema::hasColumn('lost_items', 'location_lat')) {
                $table->decimal('location_lat', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('lost_items', 'location_lng')) {
                $table->decimal('location_lng', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('lost_items', 'area')) {
                $table->string('area')->nullable();
            }
            if (!Schema::hasColumn('lost_items', 'landmarks')) {
                $table->text('landmarks')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->dropColumn([
                'location_type',
                'location_address',
                'location_lat',
                'location_lng',
                'area',
                'landmarks'
            ]);
        });
    }
};
