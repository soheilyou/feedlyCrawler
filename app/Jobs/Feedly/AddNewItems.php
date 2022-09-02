<?php

namespace App\Jobs\Feedly;

use App\Classes\Feedly\Exceptions\FeedlyException;
use App\Classes\Feedly\Feedly;
use App\Jobs\Traits\FeedlyDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewItems implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        FeedlyDispatcher;

    private array $items;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws FeedlyException
     */
    public function handle(Feedly $feedly)
    {
        /*
         * if the request fails, a FeedlyException will be thrown
         * then the job will be transferred to failed jobs (after reties)
         * in database and any data won't be lost
         */
        $feedly->addNewItems($this->items);
    }
}
