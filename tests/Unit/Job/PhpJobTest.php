<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Unit\Job;

use JobRunner\JobRunner\Job\PhpJob;
use PHPUnit\Framework\TestCase;

/** @covers \JobRunner\JobRunner\Job\PhpJob */
class PhpJobTest extends TestCase
{
    public function testOkWithDefaultValue(): void
    {
        $sUT = new PhpJob('<?php echo "toto";', 'hello', 'myName');

        self::assertSame('hello', $sUT->getCronExpression());
        self::assertSame('myName', $sUT->getName());
        self::assertSame(300, $sUT->getTtl());
        self::assertTrue($sUT->isAutoRelease());

        $process = $sUT->getProcess();
        $process->mustRun();

        self::assertSame('toto', $process->getOutput());
    }

    public function testOkWithValue(): void
    {
        $sUT = new PhpJob('<?php echo "toto";', 'hello', 'myName', 20, false);

        self::assertSame('hello', $sUT->getCronExpression());
        self::assertSame('myName', $sUT->getName());
        self::assertSame(20, $sUT->getTtl());
        self::assertFalse($sUT->isAutoRelease());

        $process = $sUT->getProcess();
        $process->mustRun();

        self::assertSame('toto', $process->getOutput());
    }
}
