<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;
use Sylius\Bundle\JobSchedulerBundle\Validator\SchedulerValidatorInterface;
use Sylius\Bundle\JobSchedulerBundle\Service\JobManagerInterface;


class JobDispatcherSpec extends ObjectBehavior
{
    function let(SchedulerValidatorInterface $validator, JobManagerInterface $jobManager)
    {
        $this->beConstructedWith($validator, $jobManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Service\JobDispatcher');
    }

    function it_should_dispatch_jobs_if_enabled_and_job_schedule_is_valid(
        SchedulerValidatorInterface $validator,
        JobManagerInterface $jobManager,
        JobInterface $job)
    {
        $validator->isSchedulerEnabled()->shouldBeCalled()->willReturn(true);
        $jobManager->findActiveJobs()->shouldBeCalled()->willReturn(array($job));
        $job->getSchedule()->willReturn('* * * * * *');
        $job->getId()->willReturn(1);
        $validator->isScheduleValid('* * * * * *')->shouldBeCalled()->willReturn(true);
        $jobManager->runJobAsync(1)->shouldBeCalled();

        $this->runActiveJobs();
    }

    function it_should_not_dispatch_jobs_if_disabled(
        SchedulerValidatorInterface $validator,
        JobManagerInterface $jobManager,
        JobInterface $job)
    {
        $validator->isSchedulerEnabled()->shouldBeCalled()->willReturn(false);
        $jobManager->findActiveJobs()->shouldNotBeCalled();

        $this->runActiveJobs();
    }

    function it_should_not_dispatch_jobs_if_enabled_and_job_schedule_is_invalid(
        SchedulerValidatorInterface $validator,
        JobManagerInterface $jobManager,
        JobInterface $job)
    {
        $validator->isSchedulerEnabled()->shouldBeCalled()->willReturn(true);
        $jobManager->findActiveJobs()->shouldBeCalled()->willReturn(array($job));
        $job->getSchedule()->willReturn('x');
        $job->getId()->willReturn(1);
        $validator->isScheduleValid('x')->shouldBeCalled()->willReturn(false);
        $jobManager->runJobAsync(1)->shouldNotBeCalled();

        $this->runActiveJobs();
    }
}
