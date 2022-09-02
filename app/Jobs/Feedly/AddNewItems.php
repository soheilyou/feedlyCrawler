<?php

namespace App\Jobs\Feedly;

use App\Classes\Crawler\FeedCrawler;
use App\Jobs\Traits\FeedlyDispatcher;
use App\Models\Feed;
use App\Repositories\Feed\FeedRepositoryInterface;
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

    public int $feedId;
    public ?Feed $feed;
    public ?FeedRepositoryInterface $feedRepository;
    public ?FeedCrawler $feedCrawler;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    }
}
