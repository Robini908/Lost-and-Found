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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();  // e.g., 'messages', 'validation', etc.
            $table->string('key');             // Translation key
            $table->json('text');              // JSON field to store translations in different languages
            $table->string('namespace')->default('*');
            $table->timestamps();

            $table->unique(['group', 'key', 'namespace']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
