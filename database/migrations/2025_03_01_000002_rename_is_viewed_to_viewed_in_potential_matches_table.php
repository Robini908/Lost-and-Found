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
        Schema::table('potential_matches', function (Blueprint $table) {
            $table->renameColumn('is_viewed', 'viewed');
            $table->dropIndex(['is_viewed', 'is_confirmed']);
            $table->index(['viewed', 'is_confirmed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('potential_matches', function (Blueprint $table) {
            $table->renameColumn('viewed', 'is_viewed');
            $table->dropIndex(['viewed', 'is_confirmed']);
            $table->index(['is_viewed', 'is_confirmed']);
        });
    }
};
