<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Payment\Exception;

use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UnresolvedDefaultPaymentMethodExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UnresolvedDefaultPaymentMethodException::class);
    }

    function it_is_an_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_custom_message()
    {
        $this->getMessage()->shouldReturn('Default payment method could not be resolved!');
    }
}
