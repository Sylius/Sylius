<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class ScheduleValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Validator\ScheduleValidator');
    }
}
