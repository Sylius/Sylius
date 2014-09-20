<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\JobSchedulerBundle\Validator\ValidatorInterface;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;


class SchedulerValidatorSpec extends ObjectBehavior
{
    function let(
        SettingsManagerInterface $settingsManager,
        ValidatorInterface $scheduleValidator
    )
    {
        $this->beConstructedWith($settingsManager, $scheduleValidator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Validator\SchedulerValidator');
    }

    function it_implements_scheduler_validator_interface_interface()
    {
        $this->shouldImplement('Sylius\Bundle\JobSchedulerBundle\Validator\SchedulerValidatorInterface');
    }
}
