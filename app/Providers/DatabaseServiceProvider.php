<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enable strict mode for MySQL
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('SET SESSION sql_mode = "STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION"');
        }

        // Log slow queries in development environment
        if (app()->environment(['local', 'development'])) {
            DB::listen(function (QueryExecuted $query) {
                if ($query->time > 100) { // Log queries that take more than 100ms
                    Log::channel('security')->warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time,
                    ]);
                }
            });
        }

        // Log potentially unsafe queries (queries without bindings)
        DB::listen(function (QueryExecuted $query) {
            $sql = strtolower($query->sql);

            // Check if this is a write operation (INSERT, UPDATE, DELETE)
            $isWriteOperation =
                str_contains($sql, 'insert') ||
                str_contains($sql, 'update') ||
                str_contains($sql, 'delete');

            // Check if query has WHERE clause but no bindings
            if ($isWriteOperation &&
                str_contains($sql, 'where') &&
                count($query->bindings) === 0) {

                Log::channel('security')->warning('Potentially unsafe query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                ]);
            }
        });
    }
}
