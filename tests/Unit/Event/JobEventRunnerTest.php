<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Event;

use JobRunner\JobRunner\Event\JobEventRunner;
use JobRunner\JobRunner\Event\JobFailEvent;
use JobRunner\JobRunner\Event\JobIsLockedEvent;
use JobRunner\JobRunner\Event\JobNotDueEvent;
use JobRunner\JobRunner\Event\JobStartEvent;
use JobRunner\JobRunner\Event\JobSuccessEvent;
use JobRunner\JobRunner\Job\Job;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JobEventRunner::class)]
class JobEventRunnerTest extends TestCase
{
    public function testOk(): void
    {
        $jobStartEvent    = self::createMock(JobStartEvent::class);
        $jobFailEvent     = self::createMock(JobFailEvent::class);
        $jobSuccessEvent  = self::createMock(JobSuccessEvent::class);
        $jobNotDueEvent   = self::createMock(JobNotDueEvent::class);
        $jobIsLockedEvent = self::createMock(JobIsLockedEvent::class);
        $job              = self::createMock(Job::class);

        $jobStartEvent->expects($this->once())->method('start')->with($job);
        $jobFailEvent->expects($this->once())->method('fail')->with($job, 'tott');
        $jobSuccessEvent->expects($this->once())->method('success')->with($job, 'titi');
        $jobNotDueEvent->expects($this->once())->method('notDue')->with($job);
        $jobIsLockedEvent->expects($this->once())->method('isLocked')->with($job);

        $sUT = new JobEventRunner($jobStartEvent, $jobFailEvent, $jobSuccessEvent, $jobNotDueEvent, $jobIsLockedEvent);

        $sUT->start($job);
        $sUT->fail($job, 'tott');
        $sUT->success($job, 'titi');
        $sUT->notDue($job);
        $sUT->isLocked($job);
    }
}
