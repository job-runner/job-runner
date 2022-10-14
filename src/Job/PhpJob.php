<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Job;

use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class PhpJob implements Job
{
    public function __construct(private string $script, private string $cronExpression, private string $name, private int $ttl = 300, private bool $autoRelease = true)
    {
    }

    public function getProcess(): Process
    {
        return new PhpProcess($this->script);
    }

    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function isAutoRelease(): bool
    {
        return $this->autoRelease;
    }
}
