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
use Sylius\Bundle\FlowBundle\Process\ProcessInterface;

class FilterProcessEventSpec extends ObjectBehavior
{
    public function let(ProcessInterface $process)
    {
        $this->beConstructedWith($process);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterProcessEvent');
    }

    public function it_is_a_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
    }

    public function it_has_a_progress($process)
    {
        $this->getProcess()->shouldReturn($process);
    }
}
