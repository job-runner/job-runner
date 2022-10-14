<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Exceptions;

use JobRunner\JobRunner\Exceptions\NotDueJob;
use JobRunner\JobRunner\Job\Job;
use PHPUnit\Framework\TestCase;

/** @covers \JobRunner\JobRunner\Exceptions\NotDueJob */
class NotDueJobTest extends TestCase
{
    public function testOk(): void
    {
        self::expectException(NotDueJob::class);
        self::expectExceptionMessage('job "myName" is not due');

        $job = self::createMock(Job::class);
        $job->expects($this->once())->method('getName')->willReturn('myName');

        throw NotDueJob::fromJob($job);
    }
}
