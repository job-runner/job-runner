<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Process\Dto;

use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Process\Process;

#[CoversClass(ProcessAndLock::class)]
class ProcessAndLockTest extends TestCase
{
    public function testOk(): void
    {
        $process = self::createMock(Process::class);
        $lock    = self::createMock(LockInterface::class);
        $job     = self::createMock(Job::class);

        $sUT = new ProcessAndLock($lock, $process, $job);

        self::assertSame($process, $sUT->getProcess());
        self::assertSame($lock, $sUT->getLock());
        self::assertSame($job, $sUT->getJob());
    }
}
