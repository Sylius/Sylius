<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Exception;

use PhpSpec\ObjectBehavior;

class UnexpectedTypeExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('stringValue', '\ExpectedType');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Exception\UnexpectedTypeException');
    }

    function it_extends_invalid_argument_exception()
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Expected argument of type "\ExpectedType", "string" given.');
    }
}
