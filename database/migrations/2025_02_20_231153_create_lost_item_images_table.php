
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
        Schema::create('lost_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained()->onDelete('cascade'); // Link to the lost item
            $table->string('image_path'); // Path to the image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_item_images');
    }
};

