<?php


namespace Tests\Unit\Classes\Organize;


use App\Classes\Organize\FeedlyProcess;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FeedlyProcessTest extends TestCase
{
    private int $targetFeedId;

    public function setUp(): void
    {
        parent::setUp();
        $this->targetFeedId = 1;
        Cache::delete(FeedlyProcess::PROCESS_PREFIX_KEY . $this->targetFeedId);
    }

    public function testFinishProcess()
    {
        FeedlyProcess::finishProcess($this->targetFeedId);
        $this->assertEmpty(Cache::get(FeedlyProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
    }

    public function testStartProcess()
    {
        FeedlyProcess::startProcess($this->targetFeedId);
        $this->assertEquals($this->targetFeedId, Cache::get(FeedlyProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
    }

    public function testAlreadyProcess()
    {
        $this->assertFalse(FeedlyProcess::alreadyProcessing($this->targetFeedId));
        FeedlyProcess::startProcess($this->targetFeedId);
        $this->assertTrue(FeedlyProcess::alreadyProcessing($this->targetFeedId));
    }

    public function testStartAndFinishProcess()
    {
        FeedlyProcess::startProcess($this->targetFeedId);
        $this->assertEquals($this->targetFeedId, Cache::get(FeedlyProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
        FeedlyProcess::finishProcess($this->targetFeedId);
        $this->assertEmpty(Cache::get(FeedlyProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
    }
}