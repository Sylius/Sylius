<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\EventDispatcher\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

class FilterStepEventSpec extends ObjectBehavior
{
    public function let(StepInterface $step)
    {
        $this->beConstructedWith($step);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterStepEvent');
    }

    public function it_is_a_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
    }

    public function it_has_a_progress($step)
    {
        $this->getStep()->shouldReturn($step);
    }
}
