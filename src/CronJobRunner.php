<?php

declare(strict_types=1);

namespace JobRunner\JobRunner;

use JobRunner\JobRunner\Event\JobEvent;
use JobRunner\JobRunner\Event\JobEventRunner;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\Process\CreateProcess;
use JobRunner\JobRunner\Process\WaitForJobsToEnd;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\FlockStore;

final class CronJobRunner implements JobRunner
{
    private CreateProcess $createProcess;
    private WaitForJobsToEnd $waitForJobsToEnd;
    private PersistingStoreInterface $persistingStore;
    /** @var array<array-key, JobEvent> */
    private array $jobEvent;

    public function __construct(CreateProcess $createProcess, WaitForJobsToEnd $waitForJobsToEnd, PersistingStoreInterface $persistingStore, JobEvent ...$jobEvent)
    {
        $this->createProcess    = $createProcess;
        $this->waitForJobsToEnd = $waitForJobsToEnd;
        $this->persistingStore  = $persistingStore;
        $this->jobEvent         = $jobEvent;
    }

    public static function create(): self
    {
        $persistingStore = new FlockStore();

        return new self(
            new CreateProcess(new LockFactory($persistingStore), new JobEventRunner()),
            new WaitForJobsToEnd(new JobEventRunner()),
            $persistingStore
        );
    }

    public function withEventListener(JobEvent $jobEvent): self
    {
        $this->jobEvent[] = $jobEvent;

        $jobEventRunner = new JobEventRunner(...$this->jobEvent);

        return new self(
            new CreateProcess(new LockFactory($this->persistingStore), $jobEventRunner),
            new WaitForJobsToEnd($jobEventRunner),
            $this->persistingStore,
            ...$this->jobEvent
        );
    }

    public function withPersistingStore(PersistingStoreInterface $persistingStore): self
    {
        $jobEventRunner = new JobEventRunner(...$this->jobEvent);

        return new self(
            new CreateProcess(new LockFactory($persistingStore), $jobEventRunner),
            new WaitForJobsToEnd($jobEventRunner),
            $persistingStore,
            ...$this->jobEvent
        );
    }

    public function run(JobList $jobs): void
    {
        $this->waitForJobsToEnd->__invoke($this->createProcess->__invoke($jobs));
    }
}
