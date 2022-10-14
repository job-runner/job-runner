<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Process\Dto;

use JobRunner\JobRunner\Job\Job;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Process\Process;

/** @internal */
class ProcessAndLock
{
    public function __construct(private LockInterface $lock, private Process $process, private Job $job)
    {
    }

    public function getLock(): LockInterface
    {
        return $this->lock;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getJob(): Job
    {
        return $this->job;
    }
}
