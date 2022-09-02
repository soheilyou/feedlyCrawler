<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Repositories\Feed\FeedRepositoryInterface;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * @var FeedRepositoryInterface
     */
    private FeedRepositoryInterface $feedRepository;

    public function __construct(FeedRepositoryInterface $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function store(Request $request)
    {
         // TODO :: validate
//        $request->validate([
//
//        ]);
        $this->feedRepository->addFeed(
            $request->feedly_id,
            $request->url,
            $request->rss_path,
            $request->update_period_in_minute,
            $request->last_build_date,
        );
    }
}
