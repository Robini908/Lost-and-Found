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
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_access_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('last_used_at');
            }

            if (!Schema::hasColumn('personal_access_tokens', 'token_type')) {
                $table->string('token_type')->default('read')->after('name');
            }

            if (!Schema::hasColumn('personal_access_tokens', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('expires_at');
            }

            if (!Schema::hasColumn('personal_access_tokens', 'metadata')) {
                $table->json('metadata')->nullable()->after('usage_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $columns = ['expires_at', 'token_type', 'usage_count', 'metadata'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('personal_access_tokens', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
