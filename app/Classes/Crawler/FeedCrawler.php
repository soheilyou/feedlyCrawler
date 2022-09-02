<?php

namespace App\Classes\Crawler;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Mtownsend\XmlToArray\XmlToArray;

class FeedCrawler
{
    protected ?Collection $items;
    private ?int $feedId = null;
    private bool $formatted = false;
    private bool $sorted = false;

    public function __construct()
    {
        $this->items = collect([]);
    }

    /**
     * @param int $feedId
     * @return FeedCrawler
     */
    public function setFeedId(int $feedId): FeedCrawler
    {
        $this->feedId = $feedId;
        return $this;
    }

    /**
     * @throws Exception
     */
    protected function crawl(string $url): array
    {
        $data = XmlToArray::convert(file_get_contents($url));
        if (empty($data)) {
            throw new Exception("empty response");
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    public function getItems(string $url): FeedCrawler
    {
        $this->formatted = false;
        $this->sorted = false;
        $data = $this->crawl($url);
        $items = $data["channel"]["item"];
        foreach ($items as $item) {
            $item["pubDateCarbon"] = Carbon::parse($item["pubDate"]);
            $this->items[] = $item;
        }
        return $this;
    }

    public function filterPubDate(Carbon $lastItemDate): FeedCrawler
    {
        if (!$this->sorted) {
            $this->sortItems();
        }
        $filteredItems = [];
        foreach ($this->items as $item) {
            if (
                Carbon::parse($item["pub_date"])->isBefore(
                    $lastItemDate->addSecond()
                )
            ) {
                // continue adding new items till reaching the last item
                break;
            }
            $filteredItems[] = $item;
        }
        $this->items = collect($filteredItems);
        return $this;
    }

    public function sortItems(): FeedCrawler
    {
        if (!$this->formatted) {
            $this->format();
        }
        $this->items = collect($this->items)->sortByDesc("pub_date");
        $this->sorted = true;
        return $this;
    }

    public function getResponse(): ?Collection
    {
        return $this->items;
    }

    public function format(): FeedCrawler
    {
        $this->items = $this->items->map(function ($item) {
            $filtered = [];
            $filtered["feed_id"] = $this->feedId;
            $filtered["pub_date"] = Carbon::parse(
                $item["pubDate"]
            )->toDateTimeString();
            $filtered["title"] = $item["title"];
            $filtered["link"] = $item["link"];
            $filtered["description"] = $item["description"];
            $filtered["created_at"] = now()->toDateTimeString();
            $filtered["updated_at"] = now()->toDateTimeString();
            return $filtered;
        });
        $this->formatted = true;
        return $this;
    }
}
