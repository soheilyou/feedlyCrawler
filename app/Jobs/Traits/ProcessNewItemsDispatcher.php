<?php

namespace App\Jobs\Traits;

use App\Classes\Organize\NewItemsProcess;

trait ProcessNewItemsDispatcher
{
    public static string $processNewItems = "process_new_items";

    public static function dispatchWithoutOverlap(...$args)
    {
        $feedId = $args[0];
        /**
         * if for this feed another job already is processing, we don't dispatch a new one
         */
        //        if (!NewItemsProcess::alreadyProcessing($feedId)) {
        if (true) {
            self::dispatch(...$args)->onQueue(self::$processNewItems);
        }
    }
}
