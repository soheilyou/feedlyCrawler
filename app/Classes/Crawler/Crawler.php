<?php


namespace App\Classes;


use Carbon\Carbon;
use Exception;
use Mtownsend\XmlToArray\XmlToArray;

class Crawler
{
    /**
     * @throws Exception
     */
    public function crawl(string $url): array
    {
        $data = XmlToArray::convert(file_get_contents($url));
        if (empty($data)) {
            throw new Exception('empty response');
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    public function getNewItems(string $url, Carbon $lastItemDate): array
    {
        $data = $this->crawl($url);
        $items = $data['channel']['item'];
        $newItems = [];
        foreach ($items as $item) {
            $item['pubDateCarbon'] = Carbon::parse($item['pubDate']);
            if ($item['pubDateCarbon']->isBefore($lastItemDate->addSecond())) {
                // continue adding new items till reaching the last item
                break;
            }
            $newItems[] = $item;
        }
        return $newItems;
    }
}