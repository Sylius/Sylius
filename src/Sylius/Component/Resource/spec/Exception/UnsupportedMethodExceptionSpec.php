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

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class UnsupportedMethodExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('methodName');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Exception\UnsupportedMethodException');
    }

    function it_extends_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('The method "methodName" is not supported.');
    }
}
