<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('group')->default('general');
            $table->string('type')->default('text');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'currency_symbol',
                'value' => 'KSh',
                'group' => 'rewards',
                'type' => 'text',
                'description' => 'Currency symbol for rewards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'points_per_found_item',
                'value' => '10',
                'group' => 'rewards',
                'type' => 'number',
                'description' => 'Points awarded for reporting a found item',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'points_to_currency_rate',
                'value' => '100',
                'group' => 'rewards',
                'type' => 'number',
                'description' => 'Number of points equal to one currency unit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
