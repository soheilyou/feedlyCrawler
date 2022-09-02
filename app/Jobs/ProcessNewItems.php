<?php

namespace App\Jobs;

use App\Classes\Crawler\FeedCrawler;
use App\Classes\Organize\NewItemsProcess;
use App\Jobs\Feedly\AddNewItems;
use App\Jobs\Traits\AutoRetry;
use App\Jobs\Traits\ProcessNewItemsDispatcher;
use App\Models\Feed;
use App\Repositories\Feed\FeedRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNewItems implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        ProcessNewItemsDispatcher,
        AutoRetry;

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
        $this->setJobUniqueKey($feedId);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        // prevent starting duplicate processes
        NewItemsProcess::startProcess($this->feedId);
        $this->feedRepository = resolve(FeedRepositoryInterface::class);
        $this->feedCrawler = new FeedCrawler();
        $this->feed = $this->feedRepository->findOrFail($this->feedId);
        $newItems = $this->getNewItems();
        if (!empty($newItems)) {
            // save the new items in the local database
            $this->feedRepository->saveBulkItems($newItems);
            // sent the new items to Feedly service async
            AddNewItems::dispatchBasedOnConfig($newItems);
        }
        // save the new items in the local database
        NewItemsProcess::finishProcess($this->feedId);
    }

    /**
     * @throws Exception
     */
    public function getNewItems(): array
    {
        $lastItem = $this->feedRepository->getLastItem($this->feedId);
        $lastItemDate = $lastItem
            ? Carbon::parse($lastItem->pub_date)
            : now()->subDays(30);
        return $this->feedCrawler
            ->getItems($this->feed->rss_url)
            ->setFeedId($this->feedId)
            ->filterPubDate($lastItemDate)
            ->getResponse()
            ->toArray();
    }
}
