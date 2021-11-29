<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Exceptions;

use JobRunner\JobRunner\Exceptions\DuplicateJob;
use JobRunner\JobRunner\Job\Job;
use PHPUnit\Framework\TestCase;

/**
 * @covers \JobRunner\JobRunner\Exceptions\DuplicateJob
 */
class DuplicateJobTest extends TestCase
{
    public function testOk(): void
    {
        $job = self::createMock(Job::class);

        $job->expects($this->any())->method('getName')->willReturn('hello');

        self::expectException(DuplicateJob::class);
        self::expectExceptionMessage('duplicate process "hello"');

        throw DuplicateJob::fromJob($job);
    }
}
