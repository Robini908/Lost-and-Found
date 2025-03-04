<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potential_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->decimal('similarity_score', 5, 2);
            $table->json('match_details')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index('similarity_score');
            $table->index(['is_viewed', 'is_confirmed']);
            $table->unique(['lost_item_id', 'found_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potential_matches');
    }
};
