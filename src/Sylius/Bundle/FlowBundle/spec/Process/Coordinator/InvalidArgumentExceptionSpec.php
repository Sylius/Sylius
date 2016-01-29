<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Process\Coordinator;

use PhpSpec\ObjectBehavior;

class InvalidArgumentExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Coordinator\InvalidArgumentException');
    }

    function it_is_a_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }
}
