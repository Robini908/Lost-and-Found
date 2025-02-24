<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who reported the item
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Link to the categories table
            $table->enum('status', ['lost', 'found', 'returned'])->default('lost');
            $table->string('location')->nullable(); // Location where the item was lost/found
            $table->date('date_lost');
            $table->date('date_found')->nullable();
            $table->foreignId('found_by')->nullable()->constrained('users')->onDelete('set null'); // User who found the item
            $table->foreignId('claimed_by')->nullable()->constrained('users')->onDelete('set null'); // User who claimed the item
            $table->string('condition')->default('good'); // Condition of the item
            $table->decimal('value', 10, 2)->nullable(); // Estimated value of the item
            $table->boolean('is_anonymous')->default(false); // Whether the report is anonymous
            $table->boolean('is_verified')->default(false); // Whether ownership is verified
            $table->date('expiry_date')->nullable(); // Expiry date for unclaimed items
            $table->json('geolocation')->nullable(); // Latitude and longitude for geofencing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Remove the foreign key constraint
        Schema::table('lost_item_images', function (Blueprint $table) {
            $table->dropForeign(['lost_item_id']);
        });

        // Now drop the lost_items table
        Schema::dropIfExists('lost_items');
    }
};
