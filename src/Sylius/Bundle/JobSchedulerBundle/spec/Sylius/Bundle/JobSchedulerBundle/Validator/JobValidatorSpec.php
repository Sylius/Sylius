<?php

namespace spec\Sylius\Bundle\JobSchedulerBundle\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\JobSchedulerBundle\Validator\ValidatorInterface;
use Sylius\Bundle\JobSchedulerBundle\Entity\Job;


class JobValidatorSpec extends ObjectBehavior
{
    function let(
        ValidatorInterface $environmentValidator,
        ValidatorInterface $serverValidator
    )
    {
        $this->beConstructedWith($environmentValidator, $serverValidator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\JobSchedulerBundle\Validator\JobValidator');
    }

    function it_implements_job_validator_interface_interface()
    {
        $this->shouldImplement('Sylius\Bundle\JobSchedulerBundle\Validator\JobValidatorInterface');
    }


    function it_should_return_true_for_a_valid_environment(
        Job $job,
        ValidatorInterface $environmentValidator,
        ValidatorInterface $serverValidator)
    {
        $environmentValidator->isValid(argument::any())->willReturn(true);
        $serverValidator->isValid(argument::any())->willReturn(true);
        $this->isValid($job)->shouldReturn(true);
    }

    function it_should_return_false_for_an_invalid_environment(
        Job $job,
        ValidatorInterface $environmentValidator,
        ValidatorInterface $serverValidator)
    {
        $environmentValidator->isValid(argument::any())->willReturn(true);
        $serverValidator->isValid(argument::any())->willReturn(false);
        $this->isValid($job)->shouldReturn(false);
    }
}
