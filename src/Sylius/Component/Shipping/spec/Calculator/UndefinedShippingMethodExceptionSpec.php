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

namespace spec\Sylius\Component\Shipping\Calculator;

use PhpSpec\ObjectBehavior;

final class UndefinedShippingMethodExceptionSpec extends ObjectBehavior
{
    public function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Throwable::class);
    }

    public function it_is_a_invalid_argument_exception(): void
    {
        $this->shouldHaveType('InvalidArgumentException');
    }
}
