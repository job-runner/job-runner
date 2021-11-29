<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Process;

use JobRunner\JobRunner\Event\JobEventRunner;
use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use JobRunner\JobRunner\Process\Dto\ProcessAndLockList;
use JobRunner\JobRunner\Process\WaitForJobsToEnd;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Process\Process;

/**
 * @covers \JobRunner\JobRunner\Process\WaitForJobsToEnd
 */
class WaitForJobsToEndTest extends TestCase
{
    public function testOkWithTwoElements(): void
    {
        $jovEventRunner     = self::createMock(JobEventRunner::class);
        $processAndLock     = self::createMock(ProcessAndLock::class);
        $processAndLock2    = self::createMock(ProcessAndLock::class);
        $process            = self::createMock(Process::class);
        $process2           = self::createMock(Process::class);
        $lock               = self::createMock(LockInterface::class);
        $lock2              = self::createMock(LockInterface::class);
        $processAndLockList = self::createMock(ProcessAndLockList::class);
        $job                = self::createMock(Job::class);

        $processAndLock->expects($this->any())->method('getProcess')->willReturn($process);
        $processAndLock->expects($this->once())->method('getLock')->willReturn($lock);
        $processAndLock->expects($this->any())->method('getJob')->willReturn($job);
        $process->expects($this->once())->method('isRunning')->willReturn(false);
        $process->expects($this->once())->method('getOutput')->willReturn('test');
        $process->expects($this->once())->method('isSuccessful')->willReturn(true);
        $processAndLock2->expects($this->any())->method('getProcess')->willReturn($process2);
        $processAndLock2->expects($this->once())->method('getLock')->willReturn($lock2);
        $processAndLock2->expects($this->any())->method('getJob')->willReturn($job);
        $process2->expects($this->once())->method('getOutput')->willReturn('test');
        $process2->expects($this->once())->method('isRunning')->willReturn(false);
        $process2->expects($this->once())->method('isSuccessful')->willReturn(true);
        $processAndLockList->expects($this->once())->method('getList')->willReturn([$processAndLock, $processAndLock2]);
        $processAndLockList->expects($this->exactly(2))->method('remove');

        $sUT = new WaitForJobsToEnd($jovEventRunner);

        $sUT->__invoke($processAndLockList);
    }

    public function testEndAtSecondIteration(): void
    {
        $jovEventRunner = self::createMock(JobEventRunner::class);
        $processAndLock = self::createMock(ProcessAndLock::class);
        $process        = self::createMock(Process::class);
        $lock           = self::createMock(LockInterface::class);
        $job            = self::createMock(Job::class);

        $processAndLock->expects($this->any())->method('getProcess')->willReturn($process);
        $processAndLock->expects($this->any())->method('getJob')->willReturn($job);
        $processAndLock->expects($this->once())->method('getLock')->willReturn($lock);
        $process->expects($this->exactly(2))->method('isRunning')->willReturnOnConsecutiveCalls(true, false);
        $process->expects($this->once())->method('getOutput')->willReturn('test');
        $process->expects($this->once())->method('isSuccessful')->willReturn(true);

        $sUT = new WaitForJobsToEnd($jovEventRunner);

        $jobList = new ProcessAndLockList();
        $jobList->push($processAndLock);

        $sUT->__invoke($jobList);
    }

    public function testOkOnSuccess(): void
    {
        $jovEventRunner = self::createMock(JobEventRunner::class);
        $processAndLock = self::createMock(ProcessAndLock::class);
        $process        = self::createMock(Process::class);
        $job            = self::createMock(Job::class);

        $job->expects($this->any())->method('getName')->willReturn('hello');
        $processAndLock->expects($this->any())->method('getProcess')->willReturn($process);
        $processAndLock->expects($this->any())->method('getJob')->willReturn($job);
        $process->expects($this->once())->method('isRunning')->willReturn(false);
        $process->expects($this->once())->method('getOutput')->willReturn('test');
        $process->expects($this->once())->method('isSuccessful')->willReturn(true);

        $jovEventRunner->expects($this->once())->method('success')->with($job, 'test');

        $sUT = new WaitForJobsToEnd($jovEventRunner);

        $jobList = new ProcessAndLockList();
        $jobList->push($processAndLock);

        $sUT->__invoke($jobList);
    }

    public function testOkOnFail(): void
    {
        $jovEventRunner = self::createMock(JobEventRunner::class);
        $processAndLock = self::createMock(ProcessAndLock::class);
        $process        = self::createMock(Process::class);
        $lock           = self::createMock(LockInterface::class);
        $job            = self::createMock(Job::class);

        $processAndLock->expects($this->any())->method('getProcess')->willReturn($process);
        $processAndLock->expects($this->once())->method('getLock')->willReturn($lock);
        $processAndLock->expects($this->any())->method('getJob')->willReturn($job);
        $process->expects($this->once())->method('isRunning')->willReturn(false);
        $process->expects($this->once())->method('getOutput')->willReturn('test');
        $process->expects($this->once())->method('isSuccessful')->willReturn(false);

        $jovEventRunner->expects($this->once())->method('fail')->with($job, 'test');

        $sUT = new WaitForJobsToEnd($jovEventRunner);

        $jobList = new ProcessAndLockList();
        $jobList->push($processAndLock);

        $sUT->__invoke($jobList);
    }
}
