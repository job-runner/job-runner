<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Process;

use JobRunner\JobRunner\Event\JobEventRunner;
use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\Process\CreateProcess;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface as OldLockInteface;
use Symfony\Component\Lock\SharedLockInterface as NewLockInterface;
use Symfony\Component\Process\Process;

use function interface_exists;

#[CoversClass(CreateProcess::class)]
class CreateProcessTest extends TestCase
{
    private function getLock(): MockObject
    {
        return self::createMock(interface_exists(NewLockInterface::class) ? NewLockInterface::class : OldLockInteface::class);
    }

    public function testNoNeedToRun(): void
    {
        $jovEventRunner = self::createMock(JobEventRunner::class);
        $job            = self::createMock(Job::class);
        $job2           = self::createMock(Job::class);
        $lockFactory    = self::createMock(LockFactory::class);

        $job->expects($this->any())->method('getName')->willReturn('myName');
        $job->expects($this->once())->method('getCronExpression')->willReturn('0 0 1 1 1');
        $job2->expects($this->any())->method('getName')->willReturn('myName2');
        $job2->expects($this->once())->method('getCronExpression')->willReturn('0 0 1 1 1');
        $jovEventRunner->expects($this->exactly(2))->method('notDue');

        $sUT = new CreateProcess($lockFactory, $jovEventRunner);

        $jobList = new JobList();
        $jobList->push($job);
        $jobList->push($job2);

        self::assertCount(0, $sUT->__invoke($jobList)->getList());
    }

    public function testLockedJob(): void
    {
        $jovEventRunner = self::createMock(JobEventRunner::class);
        $job            = self::createMock(Job::class);
        $lockFactory    = self::createMock(LockFactory::class);
        $lock           = $this->getLock();

        $job->expects($this->once())->method('getCronExpression')->willReturn('* * * * *');
        $job->expects($this->any())->method('getName')->willReturn('myName');
        $job->expects($this->once())->method('getTtl')->willReturn(30);
        $job->expects($this->once())->method('isAutoRelease')->willReturn(true);
        $lockFactory->expects($this->once())->method('createLock')->with('myName', 30, true)->willReturn($lock);
        $lock->expects($this->once())->method('acquire')->with(false)->willReturn(false);
        $jovEventRunner->expects($this->once())->method('isLocked')->with($job);

        $sUT = new CreateProcess($lockFactory, $jovEventRunner);

        $jobList = new JobList();
        $jobList->push($job);

        self::assertCount(0, $sUT->__invoke($jobList)->getList());
    }

    public function testOk(): void
    {
        $jovEventRunner = self::createMock(JobEventRunner::class);
        $job            = self::createMock(Job::class);
        $lockFactory    = self::createMock(LockFactory::class);
        $process        = self::createMock(Process::class);
        $lock           = $this->getLock();

        $job->expects($this->once())->method('getCronExpression')->willReturn('* * * * *');
        $job->expects($this->any())->method('getName')->willReturn('myName');
        $job->expects($this->once())->method('getTtl')->willReturn(30);
        $job->expects($this->once())->method('isAutoRelease')->willReturn(true);
        $job->expects($this->once())->method('getProcess')->willReturn($process);
        $process->expects($this->once())->method('start');
        $lockFactory->expects($this->once())->method('createLock')->with('myName', 30, true)->willReturn($lock);
        $lock->expects($this->once())->method('acquire')->with(false)->willReturn(true);
        $jovEventRunner->expects($this->once())->method('start')->with($job);

        $sUT = new CreateProcess($lockFactory, $jovEventRunner);

        $jobList = new JobList();
        $jobList->push($job);

        self::assertCount(1, $sUT->__invoke($jobList)->getList());
    }
}
