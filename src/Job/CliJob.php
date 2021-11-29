<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Job;

use Symfony\Component\Process\Process;

class CliJob implements Job
{
    private string $command;
    private string $cronExpression;
    private string $name;
    private int $ttl;
    private bool $autoRelease;

    public function __construct(string $command, string $cronExpression, ?string $name = null, int $ttl = 300, bool $autoRelease = true)
    {
        $this->command        = $command;
        $this->cronExpression = $cronExpression;
        $this->name           = $name ?? $command;
        $this->ttl            = $ttl;
        $this->autoRelease    = $autoRelease;
    }

    public function getProcess(): Process
    {
        return Process::fromShellCommandline($this->command);
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
