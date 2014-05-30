<?php
/**
 *
 * @author    Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @date      20/03/2014
 * @copyright Copyright (c) 2014, Reiss Ltd.
 */
namespace spec\Sylius\Bundle\JobSchedulerBundle\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;
use Doctrine\ORM\EntityManagerInterface;


class ProcessEventSpec extends ObjectBehavior
{
    function let(JobInterface $job, EntityManagerInterface $em)
    {
        $this->beConstructedWith($job, $em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Event\ProcessEvent');
    }

    function it_should_return_job(JobInterface $job)
    {
        $this->getJob()->shouldReturn($job);
    }

    function it_should_return_em(EntityManagerInterface $em)
    {
        $this->getEm()->shouldReturn($em);
    }

}
