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
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained();
            $table->string('status')->default('lost');
            $table->enum('item_type', ['reported', 'searched', 'found'])->default('reported');
            $table->string('condition')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->string('location_type')->nullable();
            $table->string('location_address')->nullable();
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->string('area')->nullable();
            $table->text('landmarks')->nullable();
            $table->string('location_lost')->nullable();
            $table->string('location_found')->nullable();
            $table->date('date_lost')->nullable();
            $table->date('date_found')->nullable();
            $table->foreignId('found_by')->nullable()->constrained('users');
            $table->foreignId('claimed_by')->nullable()->constrained('users');
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->date('expiry_date')->nullable();
            $table->json('geolocation')->nullable();
            $table->foreignId('matched_found_item_id')->nullable()->constrained('lost_items');
            $table->timestamp('found_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('additional_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};
