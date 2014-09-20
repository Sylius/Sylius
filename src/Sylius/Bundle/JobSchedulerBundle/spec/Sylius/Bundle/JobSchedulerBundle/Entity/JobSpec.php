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
use Sylius\Bundle\JobSchedulerBundle\Entity\JobLogInterface;


class JobSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Entity\Job');
    }

    function it_creates_logs_collection_by_default()
    {
        $this->getLogs()->shouldHaveType('Doctrine\\Common\\Collections\\Collection');
    }

    function its_command_is_mutable()
    {
        $this->setCommand('echo "hello world"');
        $this->getCommand()->shouldReturn('echo "hello world"');
    }

    function its_description_is_mutable()
    {
        $this->setDescription('My description');
        $this->getDescription()->shouldReturn('My description');
    }

    function its_active_is_mutable()
    {
        $this->setActive(true);
        $this->getActive()->shouldReturn(true);
    }

    function its_environment_is_mutable()
    {
        $this->setEnvironment('PROD');
        $this->getEnvironment()->shouldReturn('PROD');
    }

    function its_running_is_mutable()
    {
        $this->setIsRunning(true);
        $this->getIsRunning()->shouldReturn(true);
    }

    function its_priority_is_mutable()
    {
        $this->setPriority(1);
        $this->getPriority()->shouldReturn(1);
    }

    function its_schedule_is_mutable()
    {
        $this->setSchedule('1 2 3 4 5 6');
        $this->getSchedule()->shouldReturn('1 2 3 4 5 6');
    }

    function its_server_type_is_mutable()
    {
        $this->setServerType('Master');
        $this->getServerType()->shouldReturn('Master');
    }

    function it_returns_last_log(
        JobLogInterface $log1,
        JobLogInterface $log2
    )
    {
        $this->addLog($log1);
        $this->addLog($log2);
        $this->getLastLog()->shouldReturn($log2);
    }
}
