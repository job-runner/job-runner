<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\E2e;

use JobRunner\JobRunner\CronJobRunner;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\Job\PhpJob;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
class TestPhpJob extends TestCase
{
    public function testOk(): void
    {
        $jobRunner = CronJobRunner::create()->withEventListener(new EchoEventListener());

        $jobCollection = new JobList();
        $jobCollection->push(new PhpJob('<?php echo "yo";', '* * * * *', 'php job'));

        self::expectOutputString("successphp job:yo\n");
        $jobRunner->run($jobCollection);
    }
}
