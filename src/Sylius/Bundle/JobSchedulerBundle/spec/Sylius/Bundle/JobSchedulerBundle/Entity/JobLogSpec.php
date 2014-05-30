<?php
/**
 *
 * @author    Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @date      20/03/2014
 * @copyright Copyright (c) 2014, Reiss Ltd.
 */
namespace spec\Sylius\Bundle\JobSchedulerBundle\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobStatus;


class JobLogSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Entity\JobLog');
    }

    function it_has_the_initial_status_running()
    {
        $this->getStatus()->shouldReturn(JobStatus::RUNNING);
    }

    function it_changes_status_from_running_to_success()
    {
        $this->setStatus(JobStatus::SUCCESS);
        $this->getStatus()->shouldReturn(JobStatus::SUCCESS);
    }

    function it_doesnt_change_status_from_success_to_running()
    {
        $this->setStatus(JobStatus::SUCCESS);
        $this->setStatus(JobStatus::RUNNING);
        $this->getStatus()->shouldReturn(JobStatus::SUCCESS);
    }

    function it_changes_status_from_running_to_failed()
    {
        $this->setStatus(JobStatus::FAILED);
        $this->getStatus()->shouldReturn(JobStatus::FAILED);
    }

    function it_changes_status_from_success_to_failed()
    {
        $this->setStatus(JobStatus::SUCCESS);
        $this->setStatus(JobStatus::FAILED);
        $this->getStatus()->shouldReturn(JobStatus::FAILED);
    }

    function it_doesnt_change_status_from_failed()
    {
        $this->setStatus(JobStatus::FAILED);

        $this->setStatus(JobStatus::SUCCESS);
        $this->getStatus()->shouldReturn(JobStatus::FAILED);

        $this->setStatus(JobStatus::RUNNING);
        $this->getStatus()->shouldReturn(JobStatus::FAILED);
    }

    /*    function it_returns_empty_running_time_when_job_has_not_finished()
        {

            $this->getSchedule()->shouldReturn(null);
        }*/

    /*    function it_returns_running_time_When_job_has_finished()
        {
            $this->setFinishedAt(new \DateTime('2014-03-19 15:43:22'));
            $this->setCreatedAt(new \DateTime('2014-03-19 15:40:00'));
            $this->getSchedule()->shouldReturn(202);

            $this->setFinishedAt(new \DateTime('2014-03-20 15:40:00'));
            $this->setCreatedAt(new \DateTime('2014-03-19 15:40:00'));
            $this->getSchedule()->shouldReturn(86400);

            $this->setFinishedAt(new \DateTime('2014-03-19 15:40:22'));
            $this->setCreatedAt(new \DateTime('2014-03-19 15:40:22'));
            $this->getSchedule()->shouldReturn(0);
        }*/

}
