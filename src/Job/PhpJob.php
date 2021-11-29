<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Job;

use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class PhpJob implements Job
{
    private string $cronExpression;
    private string $name;
    private int $ttl;
    private bool $autoRelease;
    private string $script;

    public function __construct(string $script, string $cronExpression, string $name, int $ttl = 300, bool $autoRelease = true)
    {
        $this->cronExpression = $cronExpression;
        $this->name           = $name;
        $this->ttl            = $ttl;
        $this->autoRelease    = $autoRelease;
        $this->script         = $script;
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
