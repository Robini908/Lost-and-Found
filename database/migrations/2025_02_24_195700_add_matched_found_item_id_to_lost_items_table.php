<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMatchedFoundItemIdToLostItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Add the matched_found_item_id column
            $table->unsignedBigInteger('matched_found_item_id')->nullable()->after('item_type');

            // Add a foreign key constraint to link to the found item
            $table->foreign('matched_found_item_id')
                  ->references('id')
                  ->on('lost_items')
                  ->onDelete('set null'); // Set to null if the found item is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lost_items', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['matched_found_item_id']);

            // Drop the matched_found_item_id column
            $table->dropColumn('matched_found_item_id');
        });
    }
}
