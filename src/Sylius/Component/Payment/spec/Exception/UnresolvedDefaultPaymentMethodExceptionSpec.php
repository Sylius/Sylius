<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Payment\Exception;

use PhpSpec\ObjectBehavior;

final class UnresolvedDefaultPaymentMethodExceptionSpec extends ObjectBehavior
{
    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_custom_message(): void
    {
        $this->getMessage()->shouldReturn('Default payment method could not be resolved!');
    }
}
