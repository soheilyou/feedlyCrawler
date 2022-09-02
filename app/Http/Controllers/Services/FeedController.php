<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Repositories\Feed\FeedRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function addFeed(Request $request)
    {
        $request->validate([
            "feed" => "required|array",
            "feed.id" => "required|numeric",
            "feed.url" => "required",
        ]);

        if (!$this->feedRepository->find($request->id)) {
            $this->feedRepository->upsert(
                $request->input("feed.id"),
                $request->input("feed.url"),
                $request->input("feed.rss_path", "rss")
            );
        }
        return response()->json(["success" => true]);
    }
}
