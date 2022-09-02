<?php

namespace App\Classes\Organize;

use Illuminate\Support\Facades\Cache;

class NewItemsProcess
{
    const PROCESS_PREFIX_KEY = "feedly_process_";

    public static function alreadyProcessing(int $feedId): bool
    {
        return !empty(Cache::get(self::PROCESS_PREFIX_KEY . $feedId));
    }

    public static function startProcess(int $feedId): void
    {
        Cache::set(self::PROCESS_PREFIX_KEY . $feedId, 1);
    }

    public static function finishProcess(int $feedId): void
    {
        Cache::delete(self::PROCESS_PREFIX_KEY . $feedId);
    }
}
