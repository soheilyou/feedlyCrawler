<?php

namespace App\Jobs\Traits;

use App\Classes\Organize\NewItemsProcess;

trait FeedlyDispatcher
{
    public static string $feedlyQueue = "feedly";

    public static function dispatchBasedOnConfig(...$args)
    {
        $feedId = $args[0];
        /**
         * check config first
         */
        if (config("feedly.enable_connection")) {
            self::dispatch(...$args)->onQueue(self::$feedlyQueue);
        }
    }
}
