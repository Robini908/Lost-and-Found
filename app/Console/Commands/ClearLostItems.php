<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearLostItems extends Command
{
    protected $signature = 'lost-items:clear';
    protected $description = 'Clear all items in the lost_items table and reset IDs';

    public function handle()
    {
        DB::table('lost_items')->delete();
        DB::statement('ALTER TABLE lost_items AUTO_INCREMENT = 1');

        $this->info('All items in the lost_items table have been deleted and IDs reset.');
    }
}