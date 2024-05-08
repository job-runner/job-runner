# JobRunner

[![Build Status](https://github.com/job-runner/job-runner/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/job-runner/job-runner/actions/workflows/continuous-integration.yml)
[![Type Coverage](https://shepherd.dev/github/job-runner/job-runner/coverage.svg)](https://shepherd.dev/github/job-runner/job-runner)
[![Type Coverage](https://shepherd.dev/github/job-runner/job-runner/level.svg)](https://shepherd.dev/github/job-runner/job-runner)
[![Latest Stable Version](https://poser.pugx.org/job-runner/job-runner/v/stable)](https://packagist.org/packages/job-runner/job-runner)
[![License](https://poser.pugx.org/job-runner/job-runner/license)](https://packagist.org/packages/job-runner/job-runner)

## Install

`composer require job-runner/job-runner`

This cron job manager is inspired by <https://github.com/jobbyphp/jobby> but with various improvements

- It use `symfony/locker` so you can use the power of it
- It use `symfony/process` instead of `exec`
- It lock by task and not for all task
- It has an event manager

## Simple Sample

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;

require 'vendor/autoload.php';


$jobList = new JobList();
$jobList->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));
$jobList->push(new CliJob('php ' . __DIR__ . '/tutu2.php', '* * * * *'));

CronJobRunner::create()->run($jobList);

// or

CronJobRunner::create()->run(JobList::fromArray([
    [
        'command' => 'php ' . __DIR__ . '/tutu.php',
        'cronExpression' => '* * * * *',
    ],[
    [
        'command' => 'php ' . __DIR__ . '/tutu2.php',
        'cronExpression' => '* * * * *',
    ]
]));

````

## Using you own locker storage

````php
<?php

declare(strict_types=1);

use JobRunner\JobRunner\Job\CliJob;
use JobRunner\JobRunner\Job\JobList;
use JobRunner\JobRunner\CronJobRunner;

require 'vendor/autoload.php';


$mySymfonyLockerStore = new \Symfony\Component\Lock\Store\MongoDbStore();
$jobList = new JobList();
$jobList->push(new CliJob('php ' . __DIR__ . '/tutu.php', '* * * * *'));

CronJobRunner::create()->withPersistingStore($mySymfonyLockerStore)->run($jobList);

````

## Listening to events

there is various of event you can listen

- `JobRunner\JobRunner\Event\JobSuccessEvent`
- `JobRunner\JobRunner\Event\JobFailEvent`
- `JobRunner\JobRunner\Event\JobIsLockedEvent`
- `JobRunner\JobRunner\Event\JobNotDueEvent`
- `JobRunner\JobRunner\Event\JobStartEvent`

## Plugins

- [job-runner/psr-log-adapter](https://github.com/job-runner/psr-log-adapter) Adapter to logs events in psr/log
- [job-runner/symfony-console-adapter](https://github.com/job-runner/symfony-console-adapter) Adapter to logs events in symfony/console
- [job-runner/symfony-notifier-adapter](https://github.com/job-runner/symfony-notifier-adapter) Adapter to logs events in symfony/notifier

