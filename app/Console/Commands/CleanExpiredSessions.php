<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired sessions from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lifetime = config('session.lifetime');
        $cutoff = Carbon::now()->subMinutes($lifetime);

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $cutoff->timestamp)
            ->delete();

        $this->info("Cleaned up {$deleted} expired sessions.");
    }
}
