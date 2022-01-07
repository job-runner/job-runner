# JobRunner

[![Build Status](https://github.com/job-runner/job-runner/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/job-runner/job-runner/actions/workflows/continuous-integration.yml)
[![Type Coverage](https://shepherd.dev/github/job-runner/job-runner/coverage.svg)](https://shepherd.dev/github/job-runner/job-runner)
[![Type Coverage](https://shepherd.dev/github/job-runner/job-runner/level.svg)](https://shepherd.dev/github/job-runner/job-runner)
[![Latest Stable Version](https://poser.pugx.org/job-runner/job-runner/v/stable)](https://packagist.org/packages/job-runner/job-runner)
[![License](https://poser.pugx.org/job-runner/job-runner/license)](https://packagist.org/packages/job-runner/job-runner)


# Install

`composer require job-runner/job-runner`

This cron job manager is inspired by https://github.com/jobbyphp/jobby but with various improvements

- It use `symfony/locker` so you can use the power of it
- It use `symfony/process` instead of `exec`
- It lock by task and not for all task
- It has an event manager

# Simple Sample

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\PhpJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;

require 'vendor/autoload.php';


$jobList = new JobList();
$jobList->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));
$jobList->push(new PhpJob('<?php echo "yo";', '* * * * *', 'php job'));

CronJobRunner::create()->run($jobList);

````

# Using you own locker storage

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\PhpJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;

require 'vendor/autoload.php';


$mySymfonyLockerStore = new \Symfony\Component\Lock\Store\MongoDbStore();
$jobList = new JobList();
$jobList->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));
$jobList->push(new PhpJob('<?php echo "yo";', '* * * * *', 'php job'));

CronJobRunner::create()->withPersistingStore($mySymfonyLockerStore)->run($jobList);

````

# Listening to events

there is various of event you can listen

- `JobRunner\JobRunner\Event\JobSuccessEvent`
- `JobRunner\JobRunner\Event\JobFailEvent`
- `JobRunner\JobRunner\Event\JobIsLockedEvent`
- `JobRunner\JobRunner\Event\JobNotDueEvent`
- `JobRunner\JobRunner\Event\JobStartEvent`

# Adding logs with `psr/log`

`composer require job-runner/psr-log-adapter`

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\PhpJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;
use JobRunner\JobRunner\PsrLog\PsrLogEventListener;

require 'vendor/autoload.php';


$myLogger = new \Psr\Log\NullLogger();
$jobList = new JobList();
$jobList->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));
$jobList->push(new PhpJob('<?php echo "yo";', '* * * * *', 'php job'));

CronJobRunner::create()->withEventListener(new PsrLogEventListener($myLogger));->run($jobList);

````

# Adding console output with `symfony/console`

`composer require job-runner/symfony-console-adapter`

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;
use JobRunner\JobRunner\SymfonyConsole\SymfonyConsoleEventListener;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

require 'vendor/autoload.php';

(new SingleCommandApplication())
    ->setName('My Super Command') // Optional
    ->setVersion('1.0.0') // Optional
    ->addOption('bar', null, InputOption::VALUE_REQUIRED)
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $jobCollection = new JobList();
        $jobCollection->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));
        $jobCollection->push(new CliJob('php ' . __DIR__ . '/titi.php', '* * * * *', 'sample'));
        $jobCollection->push(new CliJob('php ' . __DIR__ . '/titi.php', '1 1 1 1 1', 'hehe'));
        $jobCollection->push(new CliJob('php ' . __DIR__ . '/arg.php', '* * * * *'));

        $section = $output->section();

        CronJobRunner::create()
            ->withEventListener(new SymfonyConsoleEventListener($section, new Table($section)))
            ->run($jobCollection);

    })
    ->run();
````

# Adding notification output with `symfony/notifier`

`composer require job-runner/symfony-notifier-adapter`

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;
use Symfony\Component\Notifier\Bridge\RocketChat\RocketChatTransport;
use Symfony\Component\Notifier\Channel\ChatChannel;
use Symfony\Component\Notifier\Notifier;

require 'vendor/autoload.php';


$rocket = new RocketChatTransport('mytoken', '#mychannel');
$rocket->setHost('chat.myhost.com');
$chat     = new ChatChannel($rocket);
$notifier = new Notifier(['chat' => $chat]);

$jobCollection = new JobList();
$jobCollection->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));

CronJobRunner::create()
    ->withEventListener((new \JobRunner\JobRunner\SymfonyNotifier\SymfonyNotifierEventListener($notifier))->withNotificationChannelFail(['chat']))
    ->run($jobCollection);

````
