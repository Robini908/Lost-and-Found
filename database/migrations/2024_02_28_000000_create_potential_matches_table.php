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
            $table->foreignId('lost_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->decimal('similarity_score', 5, 2);
            $table->boolean('viewed')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->timestamps();

            // Prevent duplicate matches
            $table->unique(['lost_item_id', 'found_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potential_matches');
    }
};
