<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Job;

use JobRunner\JobRunner\Exceptions\DuplicateJob;
use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Job\JobList;
use PHPUnit\Framework\TestCase;

/** @covers \JobRunner\JobRunner\Job\JobList */
class JobListTest extends TestCase
{
    public function testOk(): void
    {
        $first  = self::createMock(Job::class);
        $second = self::createMock(Job::class);

        $first->expects($this->any())->method('getName')->willReturn('name');
        $second->expects($this->any())->method('getName')->willReturn('name2');

        $sUT = new JobList($first);

        self::assertCount(1, $sUT->getList());
        self::assertSame([$first], $sUT->getList());

        $sUT->push($second);

        self::assertCount(2, $sUT->getList());
        self::assertSame([$first, $second], $sUT->getList());
    }

    public function testDuplicateProcess(): void
    {
        $first  = self::createMock(Job::class);
        $second = self::createMock(Job::class);

        $first->expects($this->any())->method('getName')->willReturn('name');
        $second->expects($this->any())->method('getName')->willReturn('name');

        $sUT = new JobList();
        $sUT->push($first);

        self::expectException(DuplicateJob::class);
        self::expectExceptionMessage('duplicate process "name"');

        $sUT->push($second);
    }

    public function testFromArrayEmpty(): void
    {
        $sUT = JobList::fromArray([]);

        self::assertCount(0, $sUT->getList());
    }

    public function testFromArrayWithOneJob(): void
    {
        $sUT = JobList::fromArray([
            [
                'command' => 'ffff',
                'cronExpression' => 'ffff',
                'name' => 'ffff',
                'ttl' => 100,
                'autoRelease' => true,
            ],
        ]);

        self::assertCount(1, $sUT->getList());
    }
}
