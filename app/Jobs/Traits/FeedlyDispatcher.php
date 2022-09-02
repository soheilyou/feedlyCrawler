<?php

namespace App\Jobs\Traits;

trait FeedlyDispatcher
{
    public static string $feedlyQueue = "feedly";

    public static function dispatchBasedOnConfig(...$args)
    {
        /**
         * check config first
         */
        if (config("feedly.enable_connection")) {
            self::dispatch(...$args)->onQueue(self::$feedlyQueue);
        }
    }
}
