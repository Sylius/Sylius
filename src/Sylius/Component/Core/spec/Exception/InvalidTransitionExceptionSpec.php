<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Exception;

use PhpSpec\ObjectBehavior;
use SM\SMException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class InvalidTransitionExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('start_checkout');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Exception\InvalidTransitionException');
    }

    function it_is_sm_exception()
    {
        $this->shouldHaveType(SMException::class);
    }

    function it_has_message()
    {
        $this->getMessage()->shouldReturn('Transition "start_checkout" is invalid for this state machine.');
    }
}
