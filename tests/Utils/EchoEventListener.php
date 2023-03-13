<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Tests\Utils;

use JobRunner\JobRunner\Event\JobEvent;
use JobRunner\JobRunner\Event\JobFailEvent;
use JobRunner\JobRunner\Event\JobSuccessEvent;
use JobRunner\JobRunner\Job\Job;

class EchoEventListener implements JobEvent, JobFailEvent, JobSuccessEvent
{
    public function fail(Job $job, string $output): void
    {
        echo 'fail' . $job->getName() . ':' . $output . "\n";
    }

    public function success(Job $job, string $output): void
    {
        echo 'success' . $job->getName() . ':' . $output . "\n";
    }
}
