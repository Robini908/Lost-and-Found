<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('item_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lost_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->json('claim_details')->nullable();
            $table->string('verification_method')->nullable();
            $table->text('verification_notes')->nullable();
            $table->foreignId('verifier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_notes')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->boolean('requires_in_person')->default(false);
            $table->timestamp('in_person_expiration')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_claims');
    }
};
