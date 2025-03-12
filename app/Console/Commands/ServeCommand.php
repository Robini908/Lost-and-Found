<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Illuminate\Support\Carbon;

class ServeCommand extends BaseServeCommand
{
    /**
     * The requests pool for tracking requests.
     *
     * @var array
     */
    protected $requestsPool = [];

    /**
     * Get the date from the given PHP server output.
     *
     * @param  string  $line
     * @return \Illuminate\Support\Carbon|null
     */
    protected function getDateFromLine($line)
    {
        return Carbon::now();
    }

    /**
     * Track a new request using the given port.
     *
     * @param  string  $port
     * @param  string  $file
     * @return void
     */
    protected function trackRequest($port, $file)
    {
        $this->requestsPool[$port] = [Carbon::now(), $file];
    }

    /**
     * Verify that PHP's CLI server is available on Windows.
     *
     * @return void
     */
    protected function verifyPHPVersion()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }

        parent::verifyPHPVersion();
    }
}
