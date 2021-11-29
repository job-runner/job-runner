<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit;

use JobRunner\JobRunner\CronJobRunner;
use JobRunner\JobRunner\Event\JobEvent;
use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\Process\CreateProcess;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use JobRunner\JobRunner\Process\Dto\ProcessAndLockList;
use JobRunner\JobRunner\Process\WaitForJobsToEnd;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\PersistingStoreInterface;

/**
 * @covers \JobRunner\JobRunner\CronJobRunner
 */
class CronJobRunnerTest extends TestCase
{
    public function testOk(): void
    {
        $createProcess    = self::createMock(CreateProcess::class);
        $waitForJobsToEnd = self::createMock(WaitForJobsToEnd::class);
        $jobCollection    = self::createMock(JobList::class);
        $processAndLock   = self::createMock(ProcessAndLock::class);
        $persistingStore  = self::createMock(PersistingStoreInterface::class);

        $processAndLockList = new ProcessAndLockList();
        $processAndLockList->push($processAndLock);

        $createProcess->expects($this->once())->method('__invoke')->with($jobCollection)->willReturn($processAndLockList);
        $waitForJobsToEnd->expects($this->once())->method('__invoke')->with($processAndLockList);

        $sUT = new CronJobRunner($createProcess, $waitForJobsToEnd, $persistingStore);

        $sUT->run($jobCollection);
    }

    public function testWithImmutableFactory(): void
    {
        $job             = self::createMock(Job::class);
        $jobEvent        = self::createMock(JobEvent::class);
        $persistingStore = self::createMock(PersistingStoreInterface::class);

        $job->expects($this->once())->method('getCronExpression')->willReturn('0 0 1 1 1');

        $jobCollection = new JobList();
        $jobCollection->push($job);

        CronJobRunner::create()->withEventListener($jobEvent)->withPersistingStore($persistingStore)->run($jobCollection);
    }
}
