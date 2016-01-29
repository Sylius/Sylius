<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Process\Step;

use PhpSpec\ObjectBehavior;

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
