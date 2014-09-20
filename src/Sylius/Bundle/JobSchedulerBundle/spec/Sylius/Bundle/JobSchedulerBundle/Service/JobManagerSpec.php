<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\JobSchedulerBundle\JobSchedulerEvents;
use Sylius\Bundle\JobSchedulerBundle\Event\ProcessEvent;
use Sylius\Bundle\JobSchedulerBundle\Service\JobLogFactoryInterface;
use Sylius\Bundle\JobSchedulerBundle\Validator\JobValidatorInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\JobSchedulerBundle\Entity\Repository\JobRepositoryInterface;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;


class JobManagerSpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $em,
        JobValidatorInterface $jobValidator,
        JoblogFactoryInterface $logFactory,
        KernelInterface $kernel,
        EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($em, $jobValidator, $logFactory, $kernel, $dispatcher);
    }

    function it_is_a_job_manager()
    {
        $this->shouldImplement('Sylius\Bundle\JobSchedulerBundle\Service\JobManagerInterface');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Service\JobManager');
    }


}
