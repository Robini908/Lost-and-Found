<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemTypeToLostItemsTable extends Migration
{
    public function up()
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->enum('item_type', ['reported', 'searched', 'found'])->default('reported');
        });
    }

    public function down()
    {
        Schema::table('lost_items', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
}
