<?php

namespace App\Repositories\Feed;

use App\Models\Feed;
use App\Models\Item;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FeedRepository extends BaseRepository implements FeedRepositoryInterface
{
    public function __construct(Feed $model)
    {
        parent::__construct($model);
    }

    public function upsert(
        int $id,
        string $url,
        ?string $rssPath = null,
        ?int $updatePeriodInMinute = null,
        $lastBuildDate = null
    ): Feed {
        if (is_string($lastBuildDate)) {
            $lastBuildDate = Carbon::parse($lastBuildDate);
        }
        return Feed::create([
            "id" => $id,
            "url" => $url,
            "rss_path" => $rssPath,
            "update_period_in_minute" => $updatePeriodInMinute,
            "last_build_date" => $lastBuildDate,
        ]);
    }

    public function addItem(
        int $feedId,
        string $title,
        string $link,
        string $image,
        string $description,
        $pubDate
    ): Item {
        return Item::create([
            "feed_id" => $feedId,
            "title" => $title,
            "link" => $link,
            "image" => $image,
            "description" => $description,
            "pub_date" => $pubDate,
        ]);
    }

    public function getLastItem(int $feedId)
    {
        return Item::where("feed_id", $feedId)
            ->orderBy("pub_date", "DESC")
            ->first();
    }

    public function getUpdateNeededFeeds()
    {
        return Feed::whereRaw(
            "(last_crawled_at < DATE_ADD(last_build_date, INTERVAL update_period_in_minute MINUTE) AND DATE_ADD(last_build_date, INTERVAL update_period_in_minute MINUTE) < NOW() )"
        )
            ->orWhereRaw("last_crawled_at < last_build_date")
            ->orWhere("last_crawled_at", null)
            ->orWhere("update_period_in_minute", null)
            ->orWhere("last_build_date", null)
            ->get();
    }

    public function saveBulkItems(array $items)
    {
        return Item::insert($items);
    }
}
