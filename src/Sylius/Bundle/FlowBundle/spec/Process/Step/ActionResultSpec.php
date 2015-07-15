<?php

namespace spec\Sylius\Bundle\FlowBundle\Process\Step;

use PhpSpec\ObjectBehavior;

class ActionResultSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('step_name');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    public function it_knows_the_next_step()
    {
        $this->getNextStepName()->shouldReturn('step_name');
    }
}
