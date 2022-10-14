<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Job;

use Symfony\Component\Process\Process;

class CliJob implements Job
{
    private string $name;

    public function __construct(private string $command, private string $cronExpression, string|null $name = null, private int $ttl = 300, private bool $autoRelease = true)
    {
        $this->name = $name ?? $command;
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
