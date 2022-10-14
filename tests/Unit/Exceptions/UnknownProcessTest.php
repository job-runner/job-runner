<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Exceptions;

use JobRunner\JobRunner\Exceptions\UnknownProcess;
use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use PHPUnit\Framework\TestCase;

/** @covers \JobRunner\JobRunner\Exceptions\UnknownProcess */
class UnknownProcessTest extends TestCase
{
    public function testOk(): void
    {
        self::expectException(UnknownProcess::class);
        self::expectExceptionMessage('process "myName" not found');

        $job = self::createMock(Job::class);
        $job->expects($this->once())->method('getName')->willReturn('myName');
        $processAndLock = self::createMock(ProcessAndLock::class);
        $processAndLock->expects($this->once())->method('getJob')->willReturn($job);

        throw UnknownProcess::fromProcess($processAndLock);
    }
}
