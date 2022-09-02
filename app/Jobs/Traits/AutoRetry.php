<?php

namespace App\Jobs\Traits;

use DateTime;
use Illuminate\Queue\Middleware\ThrottlesExceptions;

trait AutoRetry
{
    protected int $maxAttempts = 3;
    protected int $decayMinutes = 10;
    protected int $retryUntil = 24; // Hours

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [
            new ThrottlesExceptions($this->maxAttempts, $this->decayMinutes),
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return DateTime
     */
    public function retryUntil(): DateTime
    {
        return now()->addHours($this->retryUntil);
    }
}
