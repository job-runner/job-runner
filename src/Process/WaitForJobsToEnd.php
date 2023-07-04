<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Process;

use JobRunner\JobRunner\Event\JobEventRunner;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use JobRunner\JobRunner\Process\Dto\ProcessAndLockList;
use Symfony\Component\Clock\ClockInterface;

/** @internal */
class WaitForJobsToEnd
{
    public function __construct(
        private readonly JobEventRunner $eventRunner,
        private readonly ClockInterface $clock,
    ) {
    }

    public function __invoke(ProcessAndLockList $jobsToRun): void
    {
        do {
            $running = false;
            foreach ($jobsToRun->getList() as $process) {
                if (! $process->getProcess()->isRunning()) {
                    $this->release($process);
                    $jobsToRun->remove($process);
                    continue;
                }

                $running = true;
            }

            if (! $running) {
                continue;
            }

            $this->clock->sleep(0.100);
        } while ($running);
    }

    private function release(ProcessAndLock $process): void
    {
        if (! $process->getProcess()->isSuccessful()) {
            $this->eventRunner->fail($process->getJob(), $process->getProcess()->getOutput());
        } else {
            $this->eventRunner->success($process->getJob(), $process->getProcess()->getOutput());
        }

        $process->getLock()->release();
    }
}
