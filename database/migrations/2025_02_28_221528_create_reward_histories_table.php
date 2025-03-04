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
        Schema::create('reward_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['earned', 'converted', 'bonus', 'referral', 'expired'])->default('earned');
            $table->integer('points');
            $table->decimal('conversion_rate', 10, 2)->nullable();
            $table->decimal('converted_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('description');
            $table->enum('category', ['item_report', 'item_found', 'special_event', 'promotion', 'referral'])->nullable();
            $table->foreignId('lost_item_id')->nullable()->constrained()->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_expired')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for better performance
            $table->index(['user_id', 'type']);
            $table->index(['type', 'category']);
            $table->index('expires_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_histories');
    }
};
