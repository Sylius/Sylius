<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Exception;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UnresolvedDefaultShippingMethodExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UnresolvedDefaultShippingMethodException::class);
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_custom_message()
    {
        $this->getMessage()->shouldReturn('Default shipping method could not be resolved!');
    }
}
