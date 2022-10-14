<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Job;

use Symfony\Component\Process\Process;

use function array_key_exists;

class CliJob implements Job
{
    private const TTL_DEFAULT_VALUE          = 300;
    private const AUTO_RELEASE_DEFAULT_VALUE = true;

    private string $command;
    private string $cronExpression;
    private string $name;
    private int $ttl;
    private bool $autoRelease;

    public function __construct(string $command, string $cronExpression, ?string $name = null, int $ttl = self::TTL_DEFAULT_VALUE, bool $autoRelease = self::AUTO_RELEASE_DEFAULT_VALUE)
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

    /** @inheritDoc */
    public static function fromArray(array $job): self
    {
        $name        = array_key_exists('name', $job) ? $job['name'] : null;
        $ttl         = array_key_exists('ttl', $job) ? $job['ttl'] : self::TTL_DEFAULT_VALUE;
        $autoRelease = array_key_exists('autoRelease', $job) ? $job['autoRelease'] : self::AUTO_RELEASE_DEFAULT_VALUE;

        return new self($job['command'], $job['cronExpression'], $name, $ttl, $autoRelease);
    }
}
