<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobLogInterface;


class JobLogFactorySpec extends ObjectBehavior
{
    function let(JobLogInterface $jobLog)
    {
        $this->beConstructedWith($jobLog);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Service\JobLogFactory');
    }

    function it_is_a_job_log_factory()
    {
        $this->shouldImplement('Sylius\Bundle\JobSchedulerBundle\Service\JobLogFactoryInterface');
    }

    public function createLog(
        JobInterface $job,
        JobLogInterface $jobLog)
    {
        $jobLog->setJob($job)->shouldBeCalled();
        $jobLog->setStatus(JobStatus::SUCCESS)->shouldBeCalled();

        $this->createLog($job);
    }
}
