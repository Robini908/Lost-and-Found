<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use function base_path;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run the job every minute
        $schedule->command('items:precompute-similarity-scores')->everyMinute();

        // Add this line
        $schedule->command('sessions:clean')->daily();

        $schedule->command('category:refresh-suggestions')->everySecond();
    }

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        Commands\ServeCommand::class,
    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
