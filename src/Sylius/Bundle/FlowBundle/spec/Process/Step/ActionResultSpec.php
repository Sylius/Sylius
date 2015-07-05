<?php

namespace spec\Sylius\Bundle\FlowBundle\Process\Step;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActionResultSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('step_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    function it_knows_the_next_step()
    {
        $this->getNextStepName()->shouldReturn('step_name');
    }
}
