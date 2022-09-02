<?php


namespace Tests\Unit\Classes\Organize;


use App\Classes\Organize\NewItemsProcess;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class NewItemsProcessTest extends TestCase
{
    private int $targetFeedId;

    public function setUp(): void
    {
        parent::setUp();
        $this->targetFeedId = 1;
        Cache::delete(NewItemsProcess::PROCESS_PREFIX_KEY . $this->targetFeedId);
    }

    public function testFinishProcess()
    {
        NewItemsProcess::finishProcess($this->targetFeedId);
        $this->assertEmpty(Cache::get(NewItemsProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
    }

    public function testStartProcess()
    {
        NewItemsProcess::startProcess($this->targetFeedId);
        $this->assertEquals($this->targetFeedId, Cache::get(NewItemsProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
    }

    public function testAlreadyProcess()
    {
        $this->assertFalse(NewItemsProcess::alreadyProcessing($this->targetFeedId));
        NewItemsProcess::startProcess($this->targetFeedId);
        $this->assertTrue(NewItemsProcess::alreadyProcessing($this->targetFeedId));
    }

    public function testStartAndFinishProcess()
    {
        NewItemsProcess::startProcess($this->targetFeedId);
        $this->assertEquals($this->targetFeedId, Cache::get(NewItemsProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
        NewItemsProcess::finishProcess($this->targetFeedId);
        $this->assertEmpty(Cache::get(NewItemsProcess::PROCESS_PREFIX_KEY . $this->targetFeedId));
    }
}