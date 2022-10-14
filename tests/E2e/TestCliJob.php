<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\E2e;

use JobRunner\JobRunner\CronJobRunner;
use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\JobList;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
class TestCliJob extends TestCase
{
    public function testOk(): void
    {
        $jobRunner = CronJobRunner::create()->withEventListener(new EchoEventListener());

        $jobCollection = new JobList();
        $jobCollection->push(new CliJob('php ' . __DIR__ . '/cliJob.php', '* * * * *', 'cli job'));

        self::expectOutputString("successcli job:yo\n");
        $jobRunner->run($jobCollection);
    }
}
