<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Exceptions;

use JobRunner\JobRunner\Exceptions\LockedJob;
use JobRunner\JobRunner\Job\Job;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LockedJob::class)]
class LockedJobTest extends TestCase
{
    public function testOk(): void
    {
        self::expectException(LockedJob::class);
        self::expectExceptionMessage('job "myName" is locked');

        $job = self::createMock(Job::class);
        $job->expects($this->once())->method('getName')->willReturn('myName');

        throw LockedJob::fromJob($job);
    }
}
