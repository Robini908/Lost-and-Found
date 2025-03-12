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
        Schema::create('item_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->float('similarity_score')->default(0);
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();

            // Add unique constraint to prevent duplicate matches
            $table->unique(['lost_item_id', 'found_item_id']);

            // Add index for faster lookups
            $table->index(['lost_item_id', 'found_item_id', 'similarity_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_matches');
    }
};
