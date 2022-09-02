<?php

namespace App\Jobs\Traits;

use DateTime;
use Illuminate\Queue\Middleware\ThrottlesExceptions;

trait AutoRetry
{
    protected int $maxAttempts = 3;
    protected int $decayMinutes = 10;
    protected int $retryUntil = 24; // Hours
    protected bool $addClassNamespaceAsPrefix = true;
    protected string $jobUniqueKey = "";

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        $middlewares = [];
        $jobUniqueKey = $this->getJobUniqueKey();
        // empty key stops all dispatched jobs with this class
        if ($jobUniqueKey) {
            $middlewares[] = (new ThrottlesExceptions(
                $this->maxAttempts,
                $this->decayMinutes
            ))->by($this->getJobUniqueKey());
        }
        return $middlewares;
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

    /**
     * pass the final unique key
     * @return string
     */
    public function getJobUniqueKey(): string
    {
        $prefix = $this->addClassNamespaceAsPrefix ? get_class($this) : "";
        return $prefix . "_" . $this->jobUniqueKey;
    }

    /**
     * just pass a simple unique key to generate the final key by the combination of the job's namespace with the given key
     * @param string $key
     * @param bool $addClassNamespaceAsPrefix
     */
    public function setJobUniqueKey(
        string $key,
        bool $addClassNamespaceAsPrefix = true
    ) {
        $this->jobUniqueKey = $key;
        $this->addClassNamespaceAsPrefix = $addClassNamespaceAsPrefix;
    }
}
