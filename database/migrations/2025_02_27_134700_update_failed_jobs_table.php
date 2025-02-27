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
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Make the uuid field nullable
            $table->string('uuid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            // Revert the uuid field to not nullable
            $table->string('uuid')->nullable(false)->change();
        });
    }
};
