<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Process\Dto;

use JobRunner\JobRunner\Exceptions\DuplicateJob;
use JobRunner\JobRunner\Exceptions\UnknownProcess;
use JobRunner\JobRunner\Job\Job;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use JobRunner\JobRunner\Process\Dto\ProcessAndLockList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessAndLockList::class)]
class ProcessAndLockListTest extends TestCase
{
    public function testOk(): void
    {
        $first   = self::createMock(ProcessAndLock::class);
        $second  = self::createMock(ProcessAndLock::class);
        $firstJ  = self::createMock(Job::class);
        $secondJ = self::createMock(Job::class);

        $firstJ->expects($this->any())->method('getName')->willReturn('first');
        $secondJ->expects($this->any())->method('getName')->willReturn('second');
        $first->expects($this->any())->method('getJob')->willReturn($firstJ);
        $second->expects($this->any())->method('getJob')->willReturn($secondJ);

        $sUT = new ProcessAndLockList();

        self::assertCount(0, $sUT->getList());

        $sUT->push($first);

        self::assertCount(1, $sUT->getList());
        self::assertSame([$first], $sUT->getList());

        $sUT->push($second);

        self::assertCount(2, $sUT->getList());
        self::assertSame([$first, $second], $sUT->getList());

        $sUT->remove($first);

        self::assertCount(1, $sUT->getList());
        self::assertSame($second, $sUT->getList()[0]);

        $sUT->remove($second);

        self::assertCount(0, $sUT->getList());

        self::expectException(UnknownProcess::class);
        self::expectExceptionMessage('process "second" not found');
        $sUT->remove($second);
    }

    public function testDuplicateProcess(): void
    {
        $first   = self::createMock(ProcessAndLock::class);
        $second  = self::createMock(ProcessAndLock::class);
        $firstJ  = self::createMock(Job::class);
        $secondJ = self::createMock(Job::class);

        $firstJ->expects($this->any())->method('getName')->willReturn('name');
        $secondJ->expects($this->any())->method('getName')->willReturn('name');
        $first->expects($this->any())->method('getJob')->willReturn($firstJ);
        $second->expects($this->any())->method('getJob')->willReturn($secondJ);

        $sUT = new ProcessAndLockList();
        $sUT->push($first);

        self::expectException(DuplicateJob::class);
        self::expectExceptionMessage('duplicate process "name"');

        $sUT->push($second);
    }
}
