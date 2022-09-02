<?php


namespace Tests\Unit\Models;


use App\Models\Feed;
use Tests\TestCase;

class FeedTest extends TestCase
{
    public function testRssUrl()
    {
        // rss_url is a generating by model accessors
        // rss_url = url + rss_path
        $feed = Feed::factory()->create([
            'url' => 'https://www.example.com',
            'rss_path' => 'rss'
        ]);
        $this->assertEquals('https://www.example.com/rss', $feed->rss_url);
    }
}