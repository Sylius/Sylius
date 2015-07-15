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
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

class ProcessValidatorSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('message', 'step_name', function () {});
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Validator\ProcessValidator');
    }

    public function it_is_process_validator()
    {
        $this->shouldImplement('Sylius\Bundle\FlowBundle\Validator\ProcessValidatorInterface');
    }

    public function its_step_name_is_mutable()
    {
        $this->setStepName('step_name')->shouldReturn($this);
        $this->getStepName()->shouldReturn('step_name');
    }

    public function its_message_is_mutable()
    {
        $this->setMessage('message')->shouldReturn($this);
        $this->getMessage()->shouldReturn('message');
    }

    public function its_validation_is_mutable()
    {
        $closure = function () {};

        $this->setValidation($closure)->shouldReturn($this);
        $this->getValidation()->shouldReturn($closure);
    }

    public function it_calls_validation_closure()
    {
        $this->setValidation(function () {
            return true;
        });

        $this->isValid()->shouldReturn(true);
    }

    public function it_has_response(StepInterface $step)
    {
        $this->setStepName('step_name');
        $step->proceed('step_name')->shouldBeCalled();

        $this->getResponse($step);
    }
}
