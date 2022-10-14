<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Job;

use JobRunner\JobRunner\Job\CliJob;
use PHPUnit\Framework\TestCase;

/**
 * @covers \JobRunner\JobRunner\Job\CliJob
 */
class CliJobTest extends TestCase
{
    public function testOkWithDefaultValue(): void
    {
        $sUT = new CliJob('toto', 'hello', 'myName');

        self::assertSame('toto', $sUT->getProcess()->getCommandLine());
        self::assertSame('hello', $sUT->getCronExpression());
        self::assertSame('myName', $sUT->getName());
        self::assertSame(300, $sUT->getTtl());
        self::assertTrue($sUT->isAutoRelease());
    }

    public function testOkWithValue(): void
    {
        $sUT = new CliJob('toto', 'hello', 'myName', 20, false);

        self::assertSame('toto', $sUT->getProcess()->getCommandLine());
        self::assertSame('hello', $sUT->getCronExpression());
        self::assertSame('myName', $sUT->getName());
        self::assertSame(20, $sUT->getTtl());
        self::assertFalse($sUT->isAutoRelease());
    }

    public function testFromArray(): void
    {
        $sUT = CliJob::fromArray([
            'command' => 'toto',
            'cronExpression' => 'hello',
            'name' => 'myName',
            'ttl' => 100,
            'autoRelease' => true,
        ]);

        self::assertSame('hello', $sUT->getCronExpression());
        self::assertSame('myName', $sUT->getName());
        self::assertSame(100, $sUT->getTtl());
        self::assertTrue($sUT->isAutoRelease());
    }
}
