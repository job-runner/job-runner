<?php

declare(strict_types=1);

namespace JobRunner\JobRunner\Process;

use JobRunner\JobRunner\Event\JobEventRunner;
use JobRunner\JobRunner\Process\Dto\ProcessAndLock;
use JobRunner\JobRunner\Process\Dto\ProcessAndLockList;

/**
 * @internal
 */
class WaitForJobsToEnd
{
    private JobEventRunner $eventRunner;

    public function __construct(JobEventRunner $eventRunner)
    {
        $this->eventRunner = $eventRunner;
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
