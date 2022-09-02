<?php


namespace Tests\Unit\Repositories\Feed;


use App\Models\Feed;
use App\Repositories\Feed\FeedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FeedRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private FeedRepository $feedRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->feedRepository = resolve(FeedRepository::class);
        $this->deleteAllFeeds();
    }

    public function deleteAllFeeds()
    {
        Feed::query()->delete();
    }

    public function testCreateFeed()
    {
        $feedlyId = 1;
        $url = 'TEST.com';
        $rssPath = 'RSs';
        $updatePeriodInMinute = 5;
        $lastBuildDate = now();

        $feed = $this->feedRepository->upsert($feedlyId, $url, $rssPath, $updatePeriodInMinute, $lastBuildDate);
        $this->assertEquals($feedlyId, $feed->feedly_id);
        $this->assertEquals(strtolower($url), $feed->url);
        $this->assertEquals(strtolower($rssPath), $feed->rss_path);
        $this->assertEquals($updatePeriodInMinute, $feed->update_period_in_minute);
        $this->assertEquals($lastBuildDate, $feed->last_build_date);
        $this->assertTrue($feed instanceof Feed);
    }

    public function testCreateFeedCheckDatabase()
    {
        $feedlyId = 1;
        $url = 'TEST.com';
        $rssPath = 'RSs';
        $updatePeriodInMinute = 5;
        $lastBuildDate = now();

        $feed = $this->feedRepository->upsert($feedlyId, $url, $rssPath, $updatePeriodInMinute, $lastBuildDate);
        // check database
        $this->assertDatabaseHas('feeds', [
            'feedly_id' => $feedlyId,
            'url' => strtolower($url),
            'rss_path' => strtolower($rssPath),
            'update_period_in_minute' => $updatePeriodInMinute,
            'last_build_date' => $lastBuildDate
        ]);
    }

    public function testGetUpdateNeededFeedsWithNullLastBuildDate()
    {
        $feed1 = Feed::factory()->create([
            'update_period_in_minute' => 5,
            'last_build_date' => null,
            'last_crawled_at' => now(),
        ]);
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        $this->assertEquals(1, count($feeds));
    }

    public function testGetUpdateNeededFeedsWithNullLastCrawledAt()
    {
        $feed1 = Feed::factory()->create([
            'update_period_in_minute' => 5,
            'last_build_date' => now(),
            'last_crawled_at' => null,
        ]);
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        $this->assertEquals(1, count($feeds));
    }

    public function testGetUpdateNeededFeedsEmpty()
    {
        $updatePeriodInMinute = 5;
        $feed1 = Feed::factory()->create([
            'update_period_in_minute' => $updatePeriodInMinute,
            'last_build_date' => now(),
            'last_crawled_at' => now(),
        ]);
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        $this->assertEmpty($feeds);
    }

    public function testGetUpdateNeededFeedsMustBeUpdated()
    {
        $updatePeriodInMinute = 5;
        $feed1 = Feed::factory()->create([
            'update_period_in_minute' => $updatePeriodInMinute,
            'last_build_date' => now()->subMinutes($updatePeriodInMinute)->subSecond(), // last build +  update period < now
            'last_crawled_at' => now()->subMinutes($updatePeriodInMinute)->addSecond(), // last build < last crawled < now
        ]);
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        $this->assertEquals(1, count($feeds));
    }

    public function testGetUpdateNeededFeedsCrawledAndStillFresh()
    {
        $updatePeriodInMinute = 5;
        $feed1 = Feed::factory()->create([
            'update_period_in_minute' => $updatePeriodInMinute,
            'last_build_date' => now()->subMinutes($updatePeriodInMinute)->addSecond(), // now < last build + update period
            'last_crawled_at' => now()->subMinutes($updatePeriodInMinute)->addSecond(), // last build < last crawled < now
        ]);
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        $this->assertEmpty($feeds);
    }

    public function testGetUpdateNeededFeedsCrawledBeforeLastBuild()
    {
        // this case is not logical but it is possible
        $updatePeriodInMinute = 5;
        $feed1 = Feed::factory()->create([
            'update_period_in_minute' => $updatePeriodInMinute,
            'last_build_date' => now()->subMinutes($updatePeriodInMinute)->addSecond(), // now < last build + update period
            'last_crawled_at' => now()->subMinutes($updatePeriodInMinute), //  last crawled < last build < now
        ]);
        $feeds = $this->feedRepository->getUpdateNeededFeeds();
        $this->assertEquals(1, count($feeds));
    }
}