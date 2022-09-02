<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNewItems;
use App\Repositories\Feed\FeedRepositoryInterface;
use Illuminate\Console\Command;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "crawl";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "crawl feeds";
    /**
     * @var FeedRepositoryInterface
     */
    private FeedRepositoryInterface $feedRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FeedRepositoryInterface $feedRepository)
    {
        parent::__construct();
        $this->feedRepository = $feedRepository;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        // get the feeds that should be updated
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        foreach ($feeds as $feed) {
            // run async processes for each feed
            ProcessNewItems::dispatchWithoutOverlap($feed->id);
        }
    }
}
