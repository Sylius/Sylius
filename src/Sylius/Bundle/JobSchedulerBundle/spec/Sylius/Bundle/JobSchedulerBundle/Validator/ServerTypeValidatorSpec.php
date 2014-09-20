<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class ServerTypeValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Validator\ServerTypeValidator');
    }

    function it_implements_server_type_validator_interface_interface()
    {
        $this->shouldImplement('Sylius\Bundle\JobSchedulerBundle\Validator\ValidatorInterface');
    }

    function it_should_return_true()
    {
        putenv('ST=TEST');
        $this->isValid('TEST')->shouldReturn(true);
    }

    function it_should_return_true_when_no_server_is_defined()
    {
        $this->isValid()->shouldReturn(true);
    }

    function it_should_return_false()
    {
        putenv('ST=TEST');
        $this->isValid('DEV')->shouldReturn(false);
    }
}
