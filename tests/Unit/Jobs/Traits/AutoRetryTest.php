<?php

namespace Tests\Unit\Jobs\Traits;

use App\Jobs\Traits\AutoRetry;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Tests\TestCase;

class AutoRetryTest extends TestCase
{
    public function testAutoRetryDefault()
    {
        $job = new class {
            use AutoRetry;
        };

        $defaultJobUniqueKey = $job->getJobUniqueKey();
        $diff = 'test';
        $job->setJobUniqueKey($diff);
        $afterAddDiff = $job->getJobUniqueKey();
        $this->assertEquals($afterAddDiff, $defaultJobUniqueKey . $diff);
        $this->assertTrue($this->hasThrottleMiddleware($job));
    }

    protected function hasThrottleMiddleware($job): bool
    {
        foreach ($job->middleware() as $middleware) {
            if ($middleware instanceof ThrottlesExceptions) {
                return true;
            }
        }
        return false;
    }

    public function testAutoRetryCallInConstruct()
    {
        $job = new class {
            use AutoRetry;

            public function __construct()
            {
                $this->setJobUniqueKey('test');
            }
        };

        $jobUniqueKey = $job->getJobUniqueKey();
        $jobNameSpace = get_class($job);
        $this->assertEquals($jobUniqueKey, $jobNameSpace . '_test');
        $this->assertTrue($this->hasThrottleMiddleware($job));
    }

    public function testAutoRetryOverrideUniqueKey()
    {
        $job = new class {
            use AutoRetry;

            public function getJobUniqueKey(): string
            {
                return 'test';
            }
        };

        $jobUniqueKey = $job->getJobUniqueKey();
        $this->assertEquals($jobUniqueKey, 'test');
        $this->assertTrue($this->hasThrottleMiddleware($job));
    }

    public function testAutoRetryWithoutUniqueKey()
    {
        $job = new class {
            use AutoRetry;

            public function getJobUniqueKey(): string
            {
                return '';
            }
        };
        $this->assertFalse($this->hasThrottleMiddleware($job));
    }
}
