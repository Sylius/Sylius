<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Calculator;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class UndefinedShippingMethodExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException');
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType('Exception');
    }

    function it_is_a_invalid_argument_exception()
    {
        $this->shouldHaveType('InvalidArgumentException');
    }
}
