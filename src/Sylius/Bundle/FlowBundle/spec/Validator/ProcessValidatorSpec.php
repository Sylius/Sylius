<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidatorInterface;

class ProcessValidatorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('message', 'step_name', function () {});
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Validator\ProcessValidator');
    }

    function it_is_process_validator()
    {
        $this->shouldImplement(ProcessValidatorInterface::class);
    }

    function its_step_name_is_mutable()
    {
        $this->setStepName('step_name')->shouldReturn($this);
        $this->getStepName()->shouldReturn('step_name');
    }

    function its_message_is_mutable()
    {
        $this->setMessage('message')->shouldReturn($this);
        $this->getMessage()->shouldReturn('message');
    }

    function its_validation_is_mutable()
    {
        $closure = function () {};

        $this->setValidation($closure)->shouldReturn($this);
        $this->getValidation()->shouldReturn($closure);
    }

    function it_calls_validation_closure(ProcessContextInterface $processContext)
    {
        $this->setValidation(function () {
            return true;
        });

        $this->isValid($processContext)->shouldReturn(true);
    }

    function it_has_response(StepInterface $step)
    {
        $this->setStepName('step_name');
        $step->proceed('step_name')->shouldBeCalled();

        $this->getResponse($step);
    }
}
