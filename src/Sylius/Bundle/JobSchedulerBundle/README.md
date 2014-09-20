# Intro to Reiss CronManager Bundle

This bundle enables you to have only one general tab entry (usually in cron for unix/linux systems) that will trigger all the jobs stored in the database.
The bundle will decide if the job needs to run or not.

**Attention**: make sure you set the server general job to a correctly chosen frequency because if there is for example
a job defined to run every minute, your general tab job needs to run at least every minute as well to
work properly.

## Features:

- Displays  table with:
    - Time expression
    - Command
    - Running Status
    - Last run status
    - Last execution and time
    - Active
- Can display logs for each job execution
- Support edit/add job entry
- Can run jobs in specific environments/server types only
- Can run a job manually
- Can disable  jobs globally
- Can deactivate each job individually
- English and spanish translations

How it works
--------
The jobs are retrieved in order of priority from the database and are then dispatched asynchronously, each job is then run synchronously.

If a job is scheduled to run while it is already running, an error log will be created and the execution will be skipped (it won't wait until the job has finished to run again).

Two validators can be defined to filter in which environments/server types the job should run (please read specific section for more information).

Usage
--------

The Symfony command that runs the jobs is 'sylius:run_active_jobs'.

Your server crontab could look something like:

```
* * * * * /path/to/php /path/to/app/console sylius:run_active_jobs
```

In case you need to run a single job synchronously from the command line it can be done like this:

```
php app/console sylius:run_single_job_synchronous ID
```

Where ID corresponds to the job id

The bundle registers two services:

```
sylius.scheduler.manager
```

- Runs active jobs

```
sylius.scheduler.job.manager
```

- Can run a  job either synchronously or asynchronously

## Installation and configuration:

### Get the bundle

#### If you are using Symfony 2.1 and more

Add JobSchedulerBundle to your composer.json and update vendors:

``` js
    "sylius/syliusjobschedulerbundle": "dev-master"
```

### Add JobSchedulerBundle to your application kernel

``` php
<?php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Sylius\Bundle\JobSchedulerBundle\JobSchedulerBundle(),
            // ...
        );
    }
```

### Import the routing configuration

Add to your `routing.yml`:

``` yml
sylius_job_scheduler:
    resource: "@JobSchedulerBundle/Resources/config/routing.yml"
    prefix: /administration
```

**IMPORTANT: IF you don't prefix the route with /administration regular users will be authorised to manage  jobs**

### Import the config configuration

Add to *imports* in your `config.yml`:

``` yml
imports:
- { resource: "@JobSchedulerBundle/Resources/config/config.yml" }
```

Validators
--------

Each job has an Environment and Server Type field. Before running a job the job validator checks if it's in a valid
environment and server type, in order to suit your needs you should implement your own validators (implementing jobValidatorInterface) and inject them in the
*sylius.job.validator* service.

Your services file could look something like this

``` yml
sylius.job.validator:
    class: %sylius.job.validator.class%
    arguments:
        environment_validator: "@my.custom.environment.validator"
        server_type_validator: "@my.custom.server.type.validator"
```

If fields are empty, by default they will evaluate to true and therefore will run in any environment and server type.

The bundle default job validators check for the value of the shell variable *ST* for server type and *ENV* for environment.

Event dispatchers
--------

The job manager dispatches two events: when the job starts (*sylius.process.started*) and when it ends (*sylius.process.ended*).

Listeners should be registered like this:

``` yml
my..started.listener:
    class: %my..started.listener.class%
    tags:
        - { name: kernel.event_listener, event:"sylius.process.started", method:"myProcessStartedEvent" }

my..ended.listener:
    class: %my..ended.listener.class%
    tags:
        - { name: kernel.event_listener, event:"sylius.process.ended", method:"myProcessEndedEvent" }
```